# Boutique Order REST API

A lightweight, secure, and transaction-safe RESTful API built with PHP and PDO MySQL. Designed as a backend micro-service for e-commerce platforms to manage product catalogs, handle stock validation, and process orders atomically.

---

## Key Features

* **RESTful Architecture:** Clear separation of concerns with structured JSON requests and responses.
* **Database Transactions:** Ensures atomic operations during order placement using `beginTransaction()`, `commit()`, and `rollBack()` to prevent race conditions or partial writes.
* **SQL Injection Prevention:** Uses PDO prepared statements and bound parameters across all database interactions.
* **Robust Input Validation & Error Handling:** Returns appropriate HTTP status codes (`200`, `201`, `400`, `404`, `500`) alongside standardized error messages.

---

## Tech Stack

* **Language:** PHP 8.x
* **Database:** MySQL / MariaDB
* **Driver:** PHP Data Objects (PDO)
* **Environment:** Apache / Nginx (XAMPP / WAMP recommended for local development)

---

## Project Structure

```text
portfolio-boutique-order-api/
├── db.php              # PDO Database Connection
├── products.php        # GET endpoint to fetch available inventory
├── create_order.php    # POST endpoint to process purchases
└── schema.sql          # Database schema (Products & Orders tables)