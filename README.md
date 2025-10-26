# Тестовое задание для Codemate Team

## Архитектура проекта

Проект построен по принципу **Service Layer** с использованием **DTO**.

Основные слои:
- **Controllers** - принимают HTTP-запросы и преобразуют их в DTO.
- **DTOs** - передают данные между слоями приложения:
  - используются вместо Laravel Request во входных данных;
  - обеспечивают валидацию и типизацию;
  - формируют ответные объекты.
- **Services** - содержат бизнес-логику (баланс, переводы, транзакции).
- **Models (Eloquent)** - работают напрямую с базой данных.

---

## Cервисы

### `BalanceService`
Сервис для работы с балансами пользователей.

**Функциональность:**
- Получение текущего баланса (`getBalance`)
- Обновление баланса (`updateBalance`)
- Блокировка баланса с помощью `lockForUpdate()` (`lockBalance`)
- Создание баланса при отсутствии и его блокировка (`getOrCreateLockedBalance`)

**Обработка ошибок:**
- При отсутствии баланса у пользователя - `ModelNotFoundException`
- При недостатке средств - `InsufficientFundsException`

---

### `TransactionService`
Сервис для создания и обработки транзакций пользователей.

**Основные методы:**

| Метод | DTO | Описание |
|--------|-----|-----------|
| `deposit(InternalTransactionDto $data)` | `InternalTransactionDto` | Пополнение счёта |
| `withdraw(InternalTransactionDto $data)` | `InternalTransactionDto` | Снятие средств |
| `transfer(TransferDto $data)` | `TransferDto` | Перевод между пользователями |

---

## DTO 

### Входящие DTO

| DTO | Назначение|
|------|-------------|
| `InternalTransactionDto` | Используется для `deposit()` и `withdraw()`|
| `TransferDto` | Используется для `transfer()` |

В DTO происходит обработка большинства ошибок:
- Проверка типа.
- Проверка обязательных полей.
- Проверка существования пользователя с данным ID в БД.
- Проверка что amount положительное и не равно 0.
- Проверка что при переводе from_user_id и to_user_id имеют разные ID.

---

### Исходящие DTO

После успешной операции сервис возвращает **информацию о транзакции** в виде DTO.

**Пример выходных данных:**
```json
{
    "id": 42,
    "user_id": 1,
    "related_user_id": 2,
    "status": "TRANSFER_OUT",
    "amount": 1000,
    "comment": "Перевод пользователю #2",
    "created_at": "2025-10-25T12:00:00Z",
    "updated_at": "2025-10-25T12:00:00Z"
}
```
---
## Setup

### Установка проекта:
```bash
git clone https://github.com/I3OJIK/codemate-test.git test  # Клонируем этот проект в папку test
cd test  # переходим в папку
```

### Разворачивание проекта:

Если порты, указанные в `docker-compose.yaml` (3007 и 3008 и 3009), свободны - можно запуститься одной командой:
```bash
make setup
```
Если нужно изменить порты:
```bash
# Добавляем в .env.example NGINX_EXTERNAL_PORT и DB_EXTERNAL_PORT и TEST_DB_EXTERNAL_PORT
```

