# laravel_quiz

laravel_quiz is a web application created using the Laravel framework with Vue 3 that allows you to create and play quizzes.

## Installation

1. Clone the repository or fork this repository:
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
10. Add admin user
    ```bash
    php artisan app:create-admin
    ```
11. Open the app in your web browser: http://localhost:8000

## Roadmap
- ~~Hints~~
- ~~Audio questions~~
- ~~Reports~~
- Support for the Telegram app. Play quizzes in Telegram chat. 
- ~~Generate questions or answers using AI~~ (Gemini - prism-php/prism)

## UI
### User Page
##### Main Page
!["Main Page"](/storage/assets/main.JPG)
##### Question Page
!["Question Page"](/storage/assets/quiz_question.JPG)
!["Question Page"](/storage/assets/quiz_question_image.JPG)
##### Dashboard
!["Dashboard"](/storage/assets/user_dashboard.JPG)

### Admin Page
##### Dashboard
!["Dashboard"](/storage/assets/admin_dashboard.JPG)
##### Quiz List
!["Quiz List"](/storage/assets/admin_quiz_list.JPG)
##### Quiz Form
!["Quiz Form"](/storage/assets/admin_quiz_form.JPG)
##### Question List
!["Question List"](/storage/assets/admin_question_list.JPG)
##### Question Form
!["Question Form"](/storage/assets/admin_question_form.JPG)