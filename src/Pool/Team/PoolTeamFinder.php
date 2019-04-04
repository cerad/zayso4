<?php
declare(strict_types=1);

namespace App\Pool\Team;

use App\Core\QueryBuilderTrait;
use App\Game\GamesConnection;

class PoolTeamFinder
{
    use QueryBuilderTrait;

    private $poolConn;

    public function __construct(GamesConnection $poolConn)
    {
        $this->poolConn = $poolConn;
    }
    public function findPoolTeams(array $criteria) : PoolTeams
    {
        $qb = $this->poolConn->createQueryBuilder();

        $qb->select('*')->from('poolTeams')->orderBy('poolTeamId');

        $whereMeta = [
            'poolTeamIds' => 'poolTeamId',
            'regTeamIds'  => 'regTeamId',
            'projectIds'  => 'projectId',
            'programs'    => 'program',
            'genders'     => 'gender',
            'ages'        => 'age',
            'divisions'   => 'division',
        ];
        list($values,$types) = $this->addWhere($qb,$whereMeta,$criteria);
        $stmt = $qb->getConnection()->executeQuery($qb->getSQL(),$values,$types);
        $poolTeams = new PoolTeams();
        while($poolTeamRow = $stmt->fetch()) { dump($poolTeamRow);
            $poolTeams[$poolTeamRow['poolTeamId']] = new PoolTeam($poolTeamRow);
        }
        return $poolTeams;
    }
}
