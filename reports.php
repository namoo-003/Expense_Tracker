<?php
// Include the connection file to connect to the database
include('connection.php');

// Fetch budget data from the database
$budgetQuery = "SELECT * FROM `budget`";
$budgetResult = mysqli_query($con, $budgetQuery);

// Initialize an array to store the budget data
$budgets = [];
$totalBudget = 0; // To keep track of the total budget
while ($row = mysqli_fetch_assoc($budgetResult)) {
    $budgets[$row['category']] = $row['planned_budget'];
    $totalBudget += $row['planned_budget']; // Add to total budget
}

// Fetch expenses data from the database
$expensesQuery = "SELECT * FROM `expenses`";
$expensesResult = mysqli_query($con, $expensesQuery);

// Initialize an array to store the total expenses per category
$expensesData = [
    'Food' => 0,
    'Stationery' => 0,
    'Housing' => 0,
    'Miscellaneous' => 0,
];

// Loop through expenses and sum up the totals per category
while ($expense = mysqli_fetch_assoc($expensesResult)) {
    $category = $expense['category'];
    $amount = $expense['amount'];

    if (isset($expensesData[$category])) {
        $expensesData[$category] += $amount;
    }
}

// Calculate total expenses
$totalExpenses = array_sum($expensesData);

// Calculate savings (Total Budget - Total Expenses)
$savings = $totalBudget - $totalExpenses;

// Calculate daily average spending (assuming 30 days in the month)
$daysInMonth = 30;
$dailyAverage = $totalExpenses / $daysInMonth;

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Expense Tracker - Reports</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
      /* Base Styling */
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      body {
        font-family: "Poppins", sans-serif;
        color: #333;
        background-color: #f9f9f9;
        line-height: 1.6;
      }
      .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background-color: #2c3e50;
        color: #ecf0f1;
      }
      .navbar-brand a {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ecf0f1;
      }
      .navbar-links {
        display: flex;
        list-style: none;
        gap: 1.5rem;
      }
      .navbar-links a:hover {
        color: #3498db;
      }
      .navbar-account .login-btn {
        padding: 0.5rem 1rem;
        background-color: #3498db;
        color: #ecf0f1;
        border-radius: 5px;
        transition: background-color 0.3s ease;
      }
      .navbar-account .login-btn:hover {
        background-color: #2980b9;
      }

      /* Main Content */
      .content {
        margin-top: 80px; /* to ensure content is not hidden behind navbar */
        padding: 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        width: 100%;
        max-width: 1200px;
        margin: 100px auto;
      }

      .reports {
        width: 100%;
        background: #fff;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
      }

      .reports h2 {
        text-align: center;
        color: #3498db;
        margin-bottom: 2rem;
      }

      .report-section {
        margin-bottom: 2rem;
      }

      .report-section h3 {
        font-size: 1.4rem;
        color: #555;
        margin-bottom: 1rem;
      }

      canvas {
        max-width: 100%;
        margin: 0 auto;
      }

      #top-categories {
        list-style: none;
        padding: 0;
      }

      #top-categories li {
        background: #eaf4fc;
        padding: 0.8rem;
        margin-bottom: 0.5rem;
        border-radius: 8px;
      }

      .category-budget {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem;
        margin: 0.5rem 0;
        background: #eaf4fc;
        border-radius: 8px;
      }

      /* Responsive Styling */
      @media (max-width: 768px) {
        .navbar-links {
          display: none;
        }
        .navbar-account {
          display: flex;
        }
      }
    </style>
  </head>
  <body>
    <nav class="navbar">
      <div class="navbar-brand">
        <a>ExpenseTracker</a>
      </div>
      <ul class="navbar-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="budget.html">Budget</a></li>
        <li><a href="daily_expense.html">Tracker</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="settings.html">Settings</a></li>
        <li><a href="index.html">Logout</a></li>
      </ul>
    </nav>
    <div class="content">
      <section class="reports">
        <h2>Your Spending Insights</h2>

        <!-- Monthly Spending Chart -->
        <div class="report-section">
          <h3>Monthly Spending Summary</h3>
          <canvas id="monthly-spending-chart"></canvas>
        </div>

        <!-- Daily Average Spending -->
        <div class="report-section">
          <h3>Daily Spending Average</h3>
          <p>
            Your average daily spending is:
            <span id="daily-average">₹<?php echo number_format($dailyAverage, 2); ?></span>
          </p>
        </div>

        <!-- Top Spending Categories -->
        <div class="report-section">
          <h3>Top Spending Categories</h3>
          <ul id="top-categories">
            <?php
              // Display the top spending categories
              foreach ($expensesData as $category => $expenseAmount) {
                if ($expenseAmount > 0) {
                  echo "<li>$category: ₹" . number_format($expenseAmount, 2) . "</li>";
                }
              }
            ?>
          </ul>
        </div>

        <!-- Savings vs. Spending Chart (Updated) -->
        <div class="report-section">
          <h3>Savings vs. Spending</h3>
          <canvas id="savings-spending-chart"></canvas>
        </div>

        <!-- Budget Display Section -->
        <div class="report-section">
          <h3>Your Budget for Categories</h3>
          <div id="category-budget-list">
            <?php
              foreach ($budgets as $category => $plannedBudget) {
                echo "<div class='category-budget'>$category: ₹" . number_format($plannedBudget, 2) . "</div>";
              }
            ?>
          </div>
        </div>
      </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Monthly Spending Chart
        const monthlySpendingCtx = document
          .getElementById("monthly-spending-chart")
          .getContext("2d");
        new Chart(monthlySpendingCtx, {
          type: "bar",
          data: {
            labels: ["Food", "Stationery", "Housing", "Miscellaneous"],
            datasets: [{
              label: "Monthly Spending (₹)",
              data: [
                <?php echo $expensesData['Food']; ?>,
                <?php echo $expensesData['Stationery']; ?>,
                <?php echo $expensesData['Housing']; ?>,
                <?php echo $expensesData['Miscellaneous']; ?>
              ],
              backgroundColor: ["#3498db", "#2ecc71", "#f1c40f", "#e74c3c"],
              borderWidth: 1,
            }],
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
              },
            },
          },
        });

        // Savings vs. Spending Chart
        const savingsSpendingCtx = document
          .getElementById("savings-spending-chart")
          .getContext("2d");

        const totalBudget = <?php echo $totalBudget; ?>;
        const totalExpenses = <?php echo $totalExpenses; ?>;
        const savings = totalBudget - totalExpenses;

        new Chart(savingsSpendingCtx, {
          type: "doughnut",
          data: {
            labels: ["Savings", "Spending"],
            datasets: [{
              label: "Savings vs Spending",
              data: [savings, totalExpenses],
              backgroundColor: ["#2ecc71", "#e74c3c"],
            }],
          },
          options: {
            responsive: true,
          },
        });
      });
    </script>
  </body>
</html>
