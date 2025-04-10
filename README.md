Framework
This project is built using the Laravel framework, an open-source PHP web application framework.

Official website: https://laravel.com/
Documentation: https://laravel.com/docs
GitHub repository: https://github.com/laravel/laravel

Frontend Technologies

Vue.js for frontend interactions
Tailwind CSS for styling
Axios for API requests

Third-Party Libraries & Services

AWS SDK for PHP: Used for AWS service interactions
Source: https://github.com/aws/aws-sdk-php
Documentation: https://docs.aws.amazon.com/sdk-for-php/

DynamoDB: Used for data storage
Documentation: https://docs.aws.amazon.com/amazondynamodb/

Modified Framework Files
The following are framework files that we have modified:

app/Http/Controllers/AuthenticatedSessionController.php: Modified authentication logic to support DynamoDB
app/Models/User.php: Adjusted user model to work with non-relational database
routes/web.php: Modified routing configuration to support custom endpoints
resources/js/components/: Frontend Vue components

Custom Code
Custom code in this project is primarily located in the following directories:

app/Http/Controllers/MusicController.php: Music-related functionality
app/Http/Controllers/DashboardController.php: Dashboard functionality (not used)
app/Models/DynamoMusic.php: Music data model
app/Models/DynamoUser.php: User data model
resources/js/pages/auth/Login.vue: Custom login page
resources/js/pages/auth/Register.vue: Custom registration page
resources/js/pages/Dashboard.vue: Main page implementation including a user area, a subscription area, and a query area
resources/js/pages/Subscriptions.vue: Subscriptions management page (not used)
resources/js/pages/Welcome.vue: Welcome page

License Information

Laravel framework is licensed under the MIT License

Authors & Contributors

Laravel community: Framework and documentation providers
