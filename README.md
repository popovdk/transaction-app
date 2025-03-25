# Система управления балансом пользователей

![demo](./docs/demo.gif)

## 1. Технические требования

### 1.1. Стек технологий
- **Бекенд:** PHP 8, Laravel ^11
- **База данных:** PostgreSQL
- **Фронтенд:** Vue 3
- **UI:** Talwind
- **Сборка ресурсов:** Vite для сборки JS и CSS/SCSS

### 1.2. Требования к базе данных
- Используйте миграции Laravel для создания структуры базы данных
- Требуемые таблицы:
  - users (информация о пользователях системы)
  - user_balances (текущий финансовый баланс)
  - transactions (история всех транзакций)

## 2. Функциональные требования

### 2.1. Интерфейс пользователя
- Авторизация/Регистрация из коробки laravel
- **Главная страница:**
  - Отображение текущего баланса пользователя
  - Список пяти последних операций
  - Автоматическое обновление данных через заданный интервал времени (AJAX)
- **Страница истории операций:**
  - Табличное представление всех операций
  - Возможность сортировки по полю "дата"
  - Функция поиска по полю "описание"

### 2.2. Бекенд функциональность
- **Консольные команды Artisan:**
  - `user:create {email} {password} {name}` - создание нового пользователя
    - Пример: `php artisan user:create user@example.com password123 "User Name"`
    - Параметры:
      - `email` - email пользователя (уникальный)
      - `password` - пароль пользователя
      - `name` - имя пользователя
    - При успешном создании выводит ID и email пользователя
    - При ошибке выводит сообщение об ошибке

  - `transaction:process {email} {amount} {type} {description}` - проведение операции по балансу пользователя
    - Пример: `php artisan transaction:process user@example.com 1000 deposit "Initial deposit"`
    - Параметры:
      - `email` - email пользователя
      - `amount` - сумма операции (положительное число)
      - `type` - тип операции (deposit - пополнение, withdrawal - списание)
      - `description` - описание операции
    - Особенности:
      - Запрещает отрицательный баланс
      - Использует транзакции для атомарности операций
      - Логирует ошибки при неудачных операциях
      - При успешном выполнении выводит ID транзакции и её статус
      - При ошибке выводит сообщение об ошибке

- **Обработка операций:**
  - Реализация обработки транзакций с использованием Laravel Queues
  - Асинхронная обработка через `ProcessTransactionJob`
  - Автоматическое обновление статуса транзакции при ошибках

## Локальный запуск приложения

Команды запуска из `Taskfile.yml`

1. Копирование .env

```
cp .env.example .env
```

2. Сборка образов проекта

```
task build
```

3. Запуск проекта

```
task dev
```

4. Миграции

```
task app -- php artisan migrate
```

5. Генерация ключа

```
task app -- php artisan key:generate
```

Приложение будет доступно по адресу http://localhost:9005

## Структура проекта

- **app/Types/** - Объекты-значения для работы с доменной логикой
  - `Money.php` - Объект для работы с денежными суммами
  - `TransactionType.php` - Типы транзакций (пополнение, списание)
  - `TransactionStatus.php` - Статусы транзакций (в обработке, выполнена, ошибка)  

- **app/Services/** - Сервисы приложения
  - `TransactionService.php` - Сервис для обработки транзакций

- **app/Http/Controllers/** - Контроллеры приложения
  - **Auth** - Авторизация/Регистрация из коробки laravel
  - `DashboardController.php` - Контроллер для главной страницы и списка транзакций

- **app/Models/** - Модели приложения
  - `Transaction.php` - Модель транзакции
  - `UserBalance.php` - Модель баланса пользователя
  - `User.php` - Модель пользователя

- **app/Jobs/** - Задачи для очереди
  - `ProcessTransactionJob.php` - Задача обработки транзакции

- **app/Console/Commands/** - Консольные команды
  - `CreateUserCommand.php` - Команда для создания пользователя
  - `ProcessTransactionCommand.php` - Команда для обработки транзакции

- **resources/js/pages/** - Vue-компоненты для страниц
  - `Transactions.vue` - Страница просмотра транзакций
  - `Dashboard.vue` - Главная страница с текущим балансом

- **docker/** - Конфигурации Docker
  - `app/` - Образ для PHP-приложения
  - `nginx/` - Образ для веб-сервера
  - `node-builder/` - Образ для сборки фронтенда
  - `php-builder/` - Образ для сборки PHP-зависимостей

- **compose.local.yml** - Конфигурация Docker Compose для локальной разработки
- **docker-bake.hcl** - Конфигурация для сборки Docker образов
- **Taskfile.yml** - Конфигурация задач для управления проектом

