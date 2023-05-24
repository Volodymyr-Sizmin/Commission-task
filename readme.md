# Commission Calculator
This is a commission calculator application that calculates commission fees based on transaction data provided in a CSV file.

# How to Run
To run the application, follow these steps:

1. Make sure you have PHP installed on your system.

2. Clone the project repository to your local machine.

3. Navigate to the project directory.

4. Run the following command to install the dependencies:
composer install
5. To initiate the calculation of commission fees, run the following command:
/bin/php src/Service/CalculationSystem.php
   This will process the transactions and display the calculated commission fees.


# How to Run Test
1. Run the following command to run the test:
bin/phpunit tests


# Functionality
The main functionality of the application is implemented in the CommissionCalculator class, which performs the calculation of commission fees based on the transaction data provided in the CSV file. The commission fees are calculated according to the given business rules:
### 1. Deposit rule
All deposits are charged 0.03% of deposit amount.

### 2. Withdraw rules
There are different calculation rules for withdraw of private and business clients.

#### 2.1 Private Clients

Commission fee - 0.3% from withdrawn amount.
1000.00 EUR for a week (from Monday to Sunday) is free of charge. Only for the first 3 withdraw operations per a week. 4th and the following operations are calculated by using the rule above (0.3%). If total free of charge amount is exceeded them commission is calculated only for the exceeded amount (i.e. up to 1000.00 EUR no commission fee is applied).

#### 2.2 Business Clients

Commission fee - 0.5% from withdrawn amount.