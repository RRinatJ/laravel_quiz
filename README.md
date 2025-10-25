# laravel_quiz

laravel_quiz is a web application created using the Laravel framework that allows you to create and play quizzes.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/RRinatJ/laravel_quiz.git
   ```
2. Navigate to the project directory:
    ```bash
    cd laravel_quiz
    ```
3. Install all required dependencies:
    ```bash
    composer install
    npm install
    ```
4. Duplicate the .env.example file and adjust your environment settings:
    ```bash
    copy .env.example .env
    ```
5. Generate an encryption key for the application:
    ```bash
    php artisan key:generate
    ```
6. Create a symbolic link for the storage folder:
   ```bash
   php artisan storage:link
   ```
7. Run the database migrations:
    ```bash
    php artisan migrate
    ```
8. Start the npm
    ```bash
    npm run dev
    ```
9. Start the local development server:
    ```bash
    php artisan serve
    ```
10. Open the app in your web browser: http://localhost:8000

## Roadmap
1. Add hints
2. Add audio questions
3. Added support for the Telegram app. Play quizzes in Telegram chat. 
4. Generate questions or answers using AI