<?php
include_once('db.php');
include_once('model.php');

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : null;

if ($user_id) {
    // Get transactions balances
    $conn = get_connect();
    $transactions = get_user_transactions_balances($user_id, $conn);
    //Create new header and html-table with new data on php:
    $html_table = "<h2>Transactions of `$user_name`</h2>\n";
    $html_table .= "<table>
    <tr>
       <th>Mounth</th>
       <th>Amount</th>
       <th>Count days</th>
    </tr> \n";
    if(count($transactions) > 0)
    {
        foreach($transactions as $month_balance)
        {
            $month = $month_balance['month'];
            $balance = number_format($month_balance['balance'], 2, '.', '');
            $count_days = $month_balance['count_days'];
            $html_table .= "    <tr>
       <td>$month</td>
       <td>$balance</td>
       <td>$count_days</td>
    </tr> \n";
        }
    }
    $html_table .= "</table>";
    echo $html_table;
}

?>

