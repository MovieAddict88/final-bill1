
import mysql.connector
from playwright.sync_api import sync_playwright
import time

# Database credentials
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'kp_db'
}

# Test data
customer_data = {
    'full_name': 'Test Customer',
    'nid': '1234567890',
    'address': '123 Test Street',
    'conn_location': 'Test Location',
    'email': 'test@example.com',
    'package_id': 1,
    'ip_address': '127.0.0.1',
    'conn_type': 'DHCP',
    'contact': '555-1234',
    'login_code': 'test_login_123'
}

payment_data = {
    'r_month': '2025-10',
    'amount': 1000
}

def run_verification():
    db_connection = None
    cursor = None
    customer_id = None
    payment_id = None

    try:
        # Connect to the database
        db_connection = mysql.connector.connect(**db_config)
        cursor = db_connection.cursor()

        # Create a test customer
        add_customer_query = ("INSERT INTO customers "
                              "(full_name, nid, address, conn_location, email, package_id, ip_address, conn_type, contact, login_code) "
                              "VALUES (%(full_name)s, %(nid)s, %(address)s, %(conn_location)s, %(email)s, %(package_id)s, %(ip_address)s, %(conn_type)s, %(contact)s, %(login_code)s)")
        cursor.execute(add_customer_query, customer_data)
        customer_id = cursor.lastrowid
        db_connection.commit()

        # Create a payment for the customer
        add_payment_query = ("INSERT INTO payments (customer_id, r_month, amount, status) "
                             "VALUES (%s, %s, %s, 'Rejected')")
        cursor.execute(add_payment_query, (customer_id, payment_data['r_month'], payment_data['amount']))
        payment_id = cursor.lastrowid
        db_connection.commit()

        with sync_playwright() as p:
            browser = p.chromium.launch(headless=False)
            page = browser.new_page()

            # Log in as the customer
            page.goto('http://localhost:8000/customer_login.php')
            page.wait_for_load_state('networkidle')
            page.fill('input[name="login_code"]', customer_data['login_code'])
            page.click('button[type="submit"]')

            # Wait for the dashboard to load
            page.wait_for_url('http://localhost:8000/customer_dashboard.php')
            page.wait_for_load_state('networkidle')

            # Take a screenshot
            screenshot_path = 'jules-scratch/verification/verification.png'
            page.screenshot(path=screenshot_path)
            print(f"Screenshot saved to {screenshot_path}")

            browser.close()

    finally:
        # Clean up the database
        if cursor and db_connection:
            if payment_id:
                cursor.execute(f"DELETE FROM payments WHERE id = {payment_id}")
            if customer_id:
                cursor.execute(f"DELETE FROM customers WHERE id = {customer_id}")
            db_connection.commit()
            cursor.close()
        if db_connection:
            db_connection.close()

if __name__ == "__main__":
    run_verification()
