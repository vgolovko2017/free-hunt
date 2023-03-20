<?php

namespace Src;

class Utils
{
    const CHART_NOT_SET_VALUE = 0;
    const CHART_LESS_THAN_500 = 1;
    const CHART_BETWEEN_500_AND_1000 = 2;
    const CHART_BETWEEN_1000_AND_5000 = 3;
    const CHART_MORE_THAN_5000 = 4;

    public static function getProjects($dbConnection, $filter = false)
    {
        $query = <<<MySql_Query
            SELECT 
                projects.id,
                projects.name as pname,
                link,
                budget,
                skills.name as sname,
                employer_login,
                employer_name
            FROM projects
                JOIN projects_skills ON projects.id = projects_skills.project_id
                JOIN skills ON projects_skills.skill_id = skills.id
MySql_Query;

        if ($filter) {
            $query .= " WHERE skills.name like '%" . htmlspecialchars($filter) . "%'";
        }

        try {
            $data = $dbConnection->prepare($query);
            $data->execute();
            $data = $data->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch (\PDOException $e) {
            die($e->getMessage());
        }

        return $data;
    }

    public static function setInitialChartData()
    {
        $arr = [];

        $arr[self::CHART_NOT_SET_VALUE] = [
            'name' => 'Not set', 'value' => 0
        ];
        $arr[self::CHART_LESS_THAN_500] = [
            'name' => '< 500 UAH', 'value' => 0
        ];
        $arr[self::CHART_BETWEEN_500_AND_1000] = [
            'name' => '500-1000 UAH', 'value' => 0
        ];
        $arr[self::CHART_BETWEEN_1000_AND_5000] = [
            'name' => '1000-5000 UAH', 'value' => 0
        ];
        $arr[self::CHART_MORE_THAN_5000] = [
            'name' => '> 5000 UAH', 'value' => 0
        ];

        return $arr;
    }

    public static function collectRangeChartDataByBudget($budget, &$arr)
    {
        if (!$budget) {
            $arr[self::CHART_NOT_SET_VALUE]['value'] += 1;
        }
        else if ($budget < 500) {
            $arr[self::CHART_LESS_THAN_500]['value'] += 1;
        }
        else if ($budget >= 500 && $budget < 1000) {
            $arr[self::CHART_BETWEEN_500_AND_1000]['value'] += 1;
        }
        else if ($budget >= 1000 && $budget <= 5000) {
            $arr[self::CHART_BETWEEN_1000_AND_5000]['value'] += 1;
        }
        else if ($budget > 5000) {
            $arr[self::CHART_MORE_THAN_5000]['value'] += 1;
        }
    }

    public static function getDefaultFilterValues()
    {
        return [
            'All',
            'PHP',
            'Web programming',
            'Website development',
            'HTML and CSS'
        ];
    }
}
