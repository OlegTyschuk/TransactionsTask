<?php

/**
 * Return a list of users whose accounts were used to conduct transactions.
 */
function get_users($conn)
{
    $users_arr = [];
    //only users with transactions
    $sql = "SELECT DISTINCT u.`id`, u.`name` 
    FROM `users` u 
    INNER JOIN `user_accounts` ua ON ua.`user_id` = u.`id`
    INNER JOIN `transactions` tf ON tf.`account_from` = ua.`id`
    INNER JOIN `transactions` tt ON tt.`account_to` = ua.`id`
    ORDER BY u.`id`";
    $res = $conn->query($sql);
    while ($row = $res->fetch()) {
        $users_arr[$row['id']] = $row['name'];
    }
    return $users_arr;
}

/**
 * Returns an array with the monthly balance of the selected user.
 */
function get_user_transactions_balances($user_id, $conn)
{
    $balaces_arr = [];
    //Request to receive months for which transactions occurred, 
    //as well as incoming and outgoing amounts to user accounts:
    $sql = "SELECT strftime('%Y-%m', t.`trdate`) AS `tr_Ym`, 
    strftime('%m', t.`trdate`) AS `tr_m`,
    (SUM(CASE WHEN uat.`user_id` = $user_id THEN t.`amount` ELSE 0 END)) AS sum_in,
    (SUM(CASE WHEN uaf.`user_id` = $user_id THEN t.`amount` ELSE 0 END)) AS sum_out,
    COUNT(DISTINCT strftime('%Y-%m-%d', t.`trdate`)) AS count_days 
    FROM `transactions` t
    INNER JOIN `user_accounts` uaf ON uaf.`id` = t.`account_from`
    INNER JOIN `user_accounts` uat ON uat.`id` = t.`account_to`
    WHERE uaf.`user_id` = $user_id OR uat.`user_id` = $user_id
    GROUP BY `tr_Ym` ";
    $res = $conn->query($sql);

    $month_names = [
        '01' => 'January',
        '02' => 'Februarry',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
    ];

    while ($row = $res->fetch()) {
        $month_num = $row['tr_m']; //current month
        $month_str = $month_names[$month_num]; //full name of the month
        $amount_in = (float) $row['sum_in']; //sum of input transactions
        $amount_out = (float) $row['sum_out']; //sum of out transactions        
        $balance = $amount_in - $amount_out;//Calculate diff of in-out transactions
        $count_days = $row['count_days']; //count uniq days in current month
        $cur_balance = ['month' => $month_str, 'balance' => $balance, 'count_days' => $count_days];
        $balaces_arr[] = $cur_balance; //add to array
    }
    return $balaces_arr;
}


