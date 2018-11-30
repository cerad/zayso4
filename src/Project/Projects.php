<?php

namespace App\Project;

/**
 * Clearly entering hack territory here
 * Need a place to start dealing with multiple projects
 *
 * @property-read array $projects
 */
class Projects
{
    private $projectConnection;

    // Order by most recent
    public $projects = [
        'AYSONationalGames2019'   => [],
        'AYSONationalOpenCup2018' => [],
        'AYSONationalOpenCup2017' => [],
        'AYSONationalGames2016'   => [],
        'AYSONationalGames2014'   => [],
    ];
    public function __construct(ProjectConnection $projectConnection)
    {
        $this->projectConnection = $projectConnection;
    }
    // todo This really does not belong here
    public function findLatestRegisteredProjectId(string $personId) : ?string
    {
        $sql = 'SELECT projectKey FROM projectPersons WHERE personKey = ?';
        $rows = $this->projectConnection->executeQuery($sql,[$personId])->fetchAll();
        $projectIds = [];
        foreach($rows as $row) {
            $projectIds[$row['projectKey']] = $row['projectKey'];
        }
        foreach($this->projects as $projectId => $projectData) {
            if (isset($projectIds[$projectId])) {
                return $projectId;
            }
        }
        return null;
    }
}