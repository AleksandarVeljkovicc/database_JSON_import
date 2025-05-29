# Import Large JSON Data into a MySQL Database

You can use basic SQL INSERT queries to manually import data from JSON into a database row. That approach works fine for inserting a few records.

But what if you’re dealing with thousands or even millions of JSON entries? Writing one query at a time becomes imposible.

This PHP application solves that problem by automating the process using a loop and prepared SQL statements. It reads structured product data from a data.json file and imports it into multiple relational tables (products, prices, gallery, attributes, and attribute_values) in a MySQL database.

This approach is scalable, secure, and ideal for scenarios where you receive structured JSON product data from APIs or third-party services and want to store them in a normalized relational database quickly.

## Run the app

Before running this application, make sure your MySQL database contains the following tables with the appropriate relationships:

products: Stores core product information like name, category, brand, stock status, etc.

prices: Stores pricing information per product, including currency label and symbol.

gallery: Stores product image URLs associated by product ID.

attributes: Defines attribute types (e.g. color, size) per product.

attribute_values: Stores values for each attribute (e.g. red, XL).

Each table is related by product_id, ensuring normalized and relational storage.

You can refer to the ERD (Entity-Relationship Diagram) below as a visual reference:

![bbbbbbbbbbbbbbbbbbb](https://github.com/user-attachments/assets/a6bf4eb6-dfde-4612-8913-e4a3ad8fb8d8)

If your database structure doesn't match this, you must either update your database or modify the application accordingly.

# Summary

This application demonstrates a practical approach to importing large JSON datasets into a relational MySQL database. However, it is not a one-size-fits-all solution. To use this tool effectively, you must create a database schema that matches your specific JSON structure. Understanding your data format is essential. You are encouraged to adapt the logic and modify the code according to your own use case. Clear comments throughout the application explain how each part works and why it’s implemented that way.
