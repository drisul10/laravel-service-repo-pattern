# Auto Dealership - Laravel Service Repository Pattern Example

This project is a practical example of a comprehensive auto dealership management system developed using the Laravel framework (version 8) and the service repository pattern. The system is designed to manage various aspects of an auto dealership, such as the inventory of vehicles and their respective sales.

The project aims to demonstrate the power and flexibility of the service repository pattern in structuring Laravel applications. It adheres to the principles of loose coupling and high cohesion, promoting maintainability and scalability of the codebase.

The application is built using PHP version 8 and utilizes MongoDB (version 4.2) as the primary database, showcasing how a NoSQL database can be used in a Laravel application.

## Features

- **Vehicle Management**: The system allows for efficient handling of the dealership's inventory of vehicles. This includes the creation, and retrieval of vehicle records.

- **Sales Management**: The system also manages the sales transactions of the dealership. It provides functionalities for recording sales, associating them with specific vehicles, and generating sales reports.

- **Pagination & Filtering**: The API endpoints are designed to handle requests with pagination and filtering parameters, enabling efficient data retrieval and display.

- **Test Coverage**: The system is equipped with unit and feature tests to ensure the reliability and robustness of the application.

- **API Documentation**: The project employs Swagger for API documentation, providing a user-friendly interface for exploring the API's capabilities.

The Auto Dealership system is a great starting point for anyone looking to understand how to effectively implement the service repository pattern in a Laravel application, and it serves as a solid foundation for building more complex systems.

## Database Structure

The MongoDB database for this project contains three collections: `users`, `vehicles`, and `sales`.

`users` has the following structure:

- _id: ObjectId
- name: String
- email: String
- password: String (Hashed)
- created_at: Date
- updated_at: Date

`vehicles` has the following structure:

- _id: ObjectId
- type: String (enum `car` or `motorcycle`)
- name: String
- release_year: Integer
- color: String
- price: Float
- engine: String
- passenger_capacity: Integer (only for type `car`)
- car_type: String (only for type `car`)
- suspension_type: String (only for type `motorcycle`)
- transmission_type: String (only for type `motorcycle`)
- created_by: ObjectId (refers to the `_id` of a user)
- created_at: Date
- updated_at: Date

`sales` has the following structure:

- _id: ObjectId
- vehicle_id: ObjectId (refers to the `_id` of a vehicle)
- sale_date: Date
- sale_price: Float
- vehicle_price: Float
- created_by: ObjectId (refers to the `_id` of a user)
- created_at: Date
- updated_at: Date

## System Requirements

- PHP >= 8.x
- Laravel 8.x
- MongoDB 4.2
- Composer
- Git

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/drisul10/laravel-service-repo-pattern.git
   ```

2. Change to the project directory:

   ```bash
   cd laravel-service-repo-pattern
   ```

3. Copy the `.env.example` file to a new file named `.env`:

   ```bash
   make env
   ```

4. Update the `.env` file with your database and other configuration settings.

5. Install the project dependencies:

   ```bash
   make install
   ```

6. Generate a new application key:

   ```bash
   make app-key
   ```

7. Run the database migrations:

   ```bash
   make migrate
   ```

8. Serve application:

   ```bash
   make serve
   ```

## API Documentation

This project uses Swagger for API documentation. You can access the Swagger UI at the `/api/documentation` endpoint of your application. For example, if your application is running at `http://localhost:8000`, you can access the Swagger UI at `http://localhost:8000/api/documentation`.

## Registration and Login

To register a new user, make a POST request to `/api/register` with the following data:

```json
{
  "name": "Your Name",
  "email": "Your Email",
  "password": "Your Password"
}
```

See API Documentation

## Available Commands

This project provides several commands, you can see all in the Makefile at the root project.

## Running Tests

You can run the project's tests using PHPUnit:

```bash
make test
```

## Contributing

If you would like to contribute to this project, please feel free to submit a pull request. If you find a bug or have a suggestion for improvement, please open an issue.
