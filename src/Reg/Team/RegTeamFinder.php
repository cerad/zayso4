<?php
declare(strict_types=1);

namespace App\Reg\Team;

use App\Core\QueryBuilderTrait;
use App\Game\GamesConnection;

class RegTeamFinder
{
    use QueryBuilderTrait;

    private $regConn;

    public function __construct(GamesConnection $regConn)
    {
        $this->regConn = $regConn;
    }
    public function findRegTeams(array $criteria) : RegTeams
    {
        $qb = $this->regConn->createQueryBuilder();

        // Select * should probably be avoided
        $qb->select('*')->from('regTeams')->orderBy('regTeamId');

        $whereMeta = [
            'regTeamIds'  => 'regTeamId',
            'projectIds'  => 'projectId',
            'programs'    => 'program',
            'genders'     => 'gender',
            'ages'        => 'age',
            'divisions'   => 'division',
        ];
        list($values,$types) = $this->addWhere($qb,$whereMeta,$criteria);
        $stmt = $qb->getConnection()->executeQuery($qb->getSQL(),$values,$types);
        $regTeams = new RegTeams();
        while($regTeamRow = $stmt->fetch()) { dump($regTeamRow);
            $regTeams[$regTeamRow['regTeamId']] = new RegTeam($regTeamRow);
        }
        if (count($regTeams) < 1) {
            return $regTeams;
        }
        // Join the pool keys, probably should use pool finder here
        /*
        $sql = 'SELECT * FROM poolTeams WHERE regTeamId IN (?) ORDER BY regTeamId,poolKey';
        $stmt = $this->gameConn->executeQuery($sql,[array_keys($regTeams)],[Connection::PARAM_STR_ARRAY]);
        while($row = $stmt->fetch()) {
            
            // Legacy stuff
            $regTeams[$row['regTeamId']]->addPoolKey($row['poolKey']);
            $regTeams[$row['regTeamId']]->addPoolTeamKey($row['poolTeamKey']);

            $poolTeam = PoolTeam::createFromArray($row);
            $regTeams[$row['regTeamId']]->addPoolTeam($poolTeam);
            
        }*/
        return $regTeams;
        //return array_values($regTeams); // Why not the id's as well?
    }
}
