# Self Hosted Whitelabel

## What is Self Hosted Whitelabel?
The Self Hosted Whitelabel panel is designed to offer you a fully customizable server management solution that aligns with your brand's identity. This versatile platform allows you to modify every aspect of the panel, including logos, color schemes, and server plans, so that it mirrors your unique style. By using Self Hosted Whitelabel, you can create a seamless and consistent experience for your clients, reinforcing your brand’s presence and professionalism.

Whether you’re a service provider looking to offer a branded management interface or a business owner who wants a tailored solution, Self Hosted Whitelabel gives you the flexibility to design an environment that truly reflects your brand. The result is a user experience that feels coherent and personalized, strengthening client loyalty and enhancing your brand’s image.

### Key Features
- **Personalize Your Panel with Your Branding:** After acquiring access, you can immediately begin customizing the panel to fit your brand’s aesthetic. Adjust elements such as logos, panel colors, and other design details to ensure that every interaction is branded consistently. This personalization extends to every part of the interface, helping to deliver a cohesive user experience.

- **Effortless Setup:** The panel is built with simplicity in mind, featuring a user-friendly interface that makes setup and management straightforward. From configuring branding elements to managing servers, billing plans, and user accounts, the platform is designed to be intuitive and accessible. This ease of use ensures that you can quickly tailor the panel to meet your specific needs without technical hassle.

- **Flexible Billing and Plan Management:** Self Hosted Whitelabel includes powerful billing management tools that make it easy to handle subscriptions and payment processes. With customizable plans, automated invoicing, and support for multiple payment methods, you can efficiently manage and adjust billing to suit your clients’ needs. This flexibility helps streamline financial operations and ensures smooth service delivery.

- **Comprehensive Server Management:** The panel provides centralized control over server operations, offering robust features for monitoring and configuration. Keep track of server performance, check operational status, and perform necessary tasks all from a single interface. This comprehensive management ensures that your servers run efficiently and any issues are addressed promptly.

- **User-Friendly Interface:** Designed to be accessible even to users with limited technical expertise, the Self Hosted Whitelabel panel features an intuitive interface that simplifies each step of server and account management. The user-friendly design means that you can handle complex tasks with ease and confidence, regardless of your technical background.

#### Installation Steps

1. **Install Project Dependency**
    ```sh
    composer install --no-dev --optimize-autoloader
    ```

2. **Copy .env.example**
    ```sh
    cp .env.example .env
    ```

3. **Generate Application Key**
    ```sh
    php artisan key:generate
    ```

4. **Set Database Details**
    ```
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=selfhosted
    DB_USERNAME=
    DB_PASSWORD=
    ```

5. **Set Redis as a Queue Driver & Cache Driver**
    ```
    QUEUE_CONNECTION=redis
    CACHE_DRIVER=redis
    ```

    **Note:** If your Redis server requires a password, include it in the configuration:
    ```
    REDIS_PASSWORD="password here"
    ```

6. **Run Migration**
    ```sh
    php artisan migrate
    ```

7. **Set Cronjob**
    ```
    * * * * * php {PROJECT DIRECTORY}/artisan schedule:run >> /dev/null 2>&1
    ```
    **Note:** Replace `{PROJECT DIRECTORY}` with the path to your project directory.

8. **Setup Horizon**
    Configure Horizon in the supervisor with the following settings:
    ```
    [program:selfhosted]
    process_name=%(program_name)s_%(process_num)02d
    command=php {PROJECT DIRECTORY}/artisan horizon
    autostart=true
    autorestart=true
    user={system user}
    numprocs=1
    redirect_stderr=true
    stdout_logfile={PROJECT DIRECTORY}/storage/logs/horizon.log
    stopwaitsecs=3600
    ```

    **Note:** Replace `{PROJECT DIRECTORY}` and `{system user}` with your actual project directory and system user details.

9. **Restart Queue & Terminate Horizon**
    ```sh
    php artisan queue:restart
    php artisan horizon:terminate
    ```