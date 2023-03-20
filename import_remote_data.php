<?php

require 'bootstrap.php';

const MAX_PROJECTS_PAGES = 20;
const URL_GET_SKILLS = "https://api.freelancehunt.com/v2/skills";
const URL_GET_PROJECTS = "https://api.freelancehunt.com/v2/projects";

importSkills($dbConnection);
importProjects($dbConnection);

die("Successful\n");

function importProjects($dbConnection)
{
    $step = 1;
    $max_step = MAX_PROJECTS_PAGES;
    $projects = httpRequest(URL_GET_PROJECTS);

    echo URL_GET_PROJECTS . "\n";

    while (++$step < $max_step) {
        foreach ($projects->data as $project) {
            $project_id = $project->id;
            $project_name = $project->attributes->name ?? null;
            $link = $project->links->self->web ?? null;
            $employer_fname =
                $project->attributes->employer->first_name ?? null;
            $employer_lname =
                $project->attributes->employer->last_name ?? null;
            $employer_name = null;

            if ($employer_fname && $employer_lname) {
                $employer_name = $employer_fname . " " . $employer_lname;
            }

            $employer_login =
                $project->attributes->employer->login ?? null;
            $budget = $project->attributes->budget->amount ?? null;
            $currency = $project->attributes->budget->currency ?? null;

            if ($budget && $currency) {
                $budget = $budget . " " . $currency;
            }
            else {
                $budget = null;
            }

            $query = <<<MySql_Query
                INSERT INTO projects set
                    id = :id,
                    name = :name,
                    link = :link,
                    budget = :budget,
                    employer_login = :employer_login,
                    employer_name = :employer_name
MySql_Query;

            try {
                $data = $dbConnection->prepare($query);
                $data->execute([
                    ':id' => $project_id,
                    ':name' => $project_name,
                    ':link' => $link,
                    ':budget' => $budget,
                    ':employer_login' => $employer_login,
                    ':employer_name' => $employer_name
                ]);
            }
            catch (\PDOException $e) {
                exit($e->getMessage());
            }

            $skills = $project->attributes->skills ?? [];

            foreach ($skills as $skill) {
                $query = <<<MySql_Query
                INSERT INTO projects_skills set
                    project_id = :project_id,
                    skill_id = :skill_id
MySql_Query;

                try {
                    $data = $dbConnection->prepare($query);
                    $data->execute([
                        ':project_id' => $project_id,
                        ':skill_id' => $skill->id
                    ]);
                }
                catch (\PDOException $e) {
                    exit($e->getMessage());
                }
            }
        }

        if (($next_url = $projects?->links?->next)) {
            echo $next_url . "\n";

            $projects = httpRequest($next_url);
            if (!$projects) {
                break;
            }
        }
        else {
            break;
        }
    }
}

function importSkills($dbConnection)
{
    $skills = httpRequest(URL_GET_SKILLS);

    foreach ($skills->data as $skill) {
        $query = "INSERT INTO skills set id = :id, name = :name";

        try {
            $data = $dbConnection->prepare($query);
            $data->execute([
                'id' => $skill->id,
                'name' => $skill->name
            ]);
        }
        catch (\PDOException $e) {
            die($e->getMessage());
        }
    }
}

function httpRequest($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $bearer = $_ENV['FREEHUNT_AUTH_BEARER'] ?? null;
    $authorization = "Authorization: Bearer " . $bearer;

    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [
            'Content-Type: application/json', $authorization
        ]
    );

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return false;
    }

    return json_decode($content);
}
