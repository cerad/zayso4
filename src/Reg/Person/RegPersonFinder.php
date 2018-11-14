<?php
namespace App\Reg\Person;

use App\Reg\RegConnection;
use App\User\UserConnection;

class RegPersonFinder
{
    private $userConn;
    private $regTeamConn;
    private $regPersonConn;

    public function __construct(
        RegConnection  $regPersonConn,
        RegConnection  $regTeamConn,
        UserConnection $userConn)
    {
        $this->userConn      = $userConn;
        $this->regTeamConn   = $regTeamConn;
        $this->regPersonConn = $regPersonConn;
    }
    public function findRegPerson(string $projectId, string $personId) : ?RegPerson
    {
        $sql = <<<EOT
SELECT
  id         AS id,
  projectKey AS projectId,
  personKey  AS personId,
  orgKey     AS orgId,
  fedKey     AS fedId,
  regYear    AS regYear,
  registered AS registered,
  verified   AS verified,
  name       AS name,
  email      AS email,
  phone      AS phone,
  gender     AS gender,
  dob        AS dob,
  age        AS age,
  shirtSize  AS shirtSize,
  notes      AS notes,
  notesUser  AS notesUser,
  plans      AS plans,
  avail      AS avail,
  createdOn  AS createdOn,
  updatedOn  AS updatedOn,
  version    AS version
FROM  projectPersons
WHERE projectKey = ? AND personKey = ?
EOT;
        $row = $this->regPersonConn->executeQuery($sql,[$projectId,$personId])->fetch();

        if (!$row) {
            return null;
        }
        $row['avail'] = unserialize($row['avail']);
        $row['plans'] = unserialize($row['plans']);

        $row['roles'] = [];
        $sql = <<<EOT
SELECT
  id              AS id,
  projectPersonId AS regPersonId,
  role         AS role,
  roleDate     AS roleDate,
  badge        AS badge,
  badgeDate    AS badgeDate,
  badgeUser    AS badgeUser,
  badgeExpires AS badgeExpires,
  active       AS active,
  approved     AS approved,
  verified     AS verified,
  ready        AS ready,
  misc         AS misc,
  notes        AS notes
FROM     projectPersonRoles
WHERE    projectPersonId = ?
ORDER BY role
EOT;
        $row['roles'] = $this->regPersonConn->executeQuery($sql,[$row['id']])->fetchAll();
        return RegPerson::create($row);
    }
    /** ==========================================
     * Mainly for crews
     * projectPersonId is an autoincrement
     * This will be cleaner once the ids have been fixed up
     *
     * @param  $projectId string
     * @param  $personId string
     * @return RegPersonPerson[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findRegPersonPersons(string $projectId, string $personId)
    {
        // Get the primary information, avoid having to make an actual primary entry for now
        $sql = <<<EOD
SELECT
  id         AS id,
  projectKey AS projectId,
  personKey  AS phyPersonId,
  name       AS name
FROM  projectPersons
WHERE projectKey = ? AND personKey = ?
EOD;
        $stmt = $this->regPersonConn->executeQuery($sql,[$projectId,$personId]);
        $primaryRow = $stmt->fetch();
        if (!$primaryRow) {
            return [];
        }
        // A person is always a member of their own crew
        $regPersonPersons[] = RegPersonPerson::create([
            'role'        => 'Primary',
            'projectId'   => $projectId,
            'managerId'   => $personId,
            'managerName' => $primaryRow['name'],
            'memberId'    => $personId,
            'memberName'  => $primaryRow['name'],
        ]);

        // Now pull the crew
        $managerId = $projectId . ':' . $personId; // Hack
        $sql = 'SELECT * FROM regPersonPersons WHERE managerId = ? ORDER BY role,memberName';
        $stmt = $this->regPersonConn->executeQuery($sql,[$managerId]);
        while($row = $stmt->fetch()) {
            $row['projectId'] = $projectId;
            $row['managerId'] = $personId;
            $row['memberId'] = explode(':',$row['memberId'])[1]; // Ultra hack
            $regPersonPersons[] = RegPersonPerson::create($row);
        }
        return $regPersonPersons;
    }
    /** ==========================================
     * Teams associated with RegPerson
     *
     * @param  $regPersonId string
     * @return RegPersonTeam[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findRegPersonTeams(string $projectId, string $personId)
    {
        $managerId = $projectId . ':' . $personId; // Hack
        $sql = 'SELECT * FROM regPersonTeams WHERE managerId = ? ORDER BY role,teamId';
        $stmt = $this->regPersonConn->executeQuery($sql,[$managerId]);
        $regPersonTeams = [];
        while($row = $stmt->fetch()) {
            $row['projectId'] = $projectId;
            $row['managerId'] = $personId;
            $row['teamId']    = explode(':',$row['teamId'])[1]; // Ultra hack
            $regPersonTeams[] = RegPersonTeam::create($row);
        }
        return $regPersonTeams;
    }
    public function findRegPersonTeamIds($regPersonId)
    {
        $sql = 'SELECT teamId FROM regPersonTeams WHERE managerId = ?';
        $stmt = $this->regPersonConn->executeQuery($sql,[$regPersonId]);
        $regPersonTeamIds = [];
        while($row = $stmt->fetch()) {
            $regPersonTeamIds[$row['teamId']] = $row['teamId'];
        }
        return $regPersonTeamIds;
    }
    /* ==========================================
     * Mainly for adding people to crews
     *
     */
    public function findRegPersonChoices($projectId)
    {
        $sql = <<<EOD
SELECT 
  personKey AS personId,
  name      AS name,
  role      AS role
FROM projectPersons AS regPerson
LEFT JOIN projectPersonRoles AS regPersonRole ON regPersonRole.projectPersonId = regPerson.id
WHERE projectKey = ? AND role = 'ROLE_REFEREE'
ORDER BY name,role
EOD;
        $stmt = $this->regPersonConn->executeQuery($sql,[$projectId]);
        $persons = [];
        while($row = $stmt->fetch())
        {
            $regPersonId = $projectId . ':' . $row['personId'];

            $persons[$regPersonId] = $row['name'];
        }
        return $persons;
    }
    /* ==========================================
     * For adding teams to person
     * This is a view routine, should it be in it's own class?
     * Showing program here, better to have a choice view column
     */
    public function findRegTeamChoices($projectId)
    {
        $sql = <<<EOD
SELECT regTeamId, teamName, program, gender, age
FROM regTeams AS regTeam
WHERE projectId = ?
ORDER BY regTeamId
EOD;
        $stmt = $this->regTeamConn->executeQuery($sql,[$projectId]);
        $choices = [];
        while($row = $stmt->fetch())
        {
            $choices[$row['regTeamId']] = sprintf('%s-%s %s',
                $row['age'],$row['gender'],$row['teamName']);
        }
        return $choices;
    }
    /* ==========================================
     * Mainly for Switch User within a project
     *
     */
    public function findUserChoices($projectId)
    {
        $sql = <<<EOD
SELECT 
  personKey AS personId,
  name      AS name,
  role      AS role
FROM projectPersons AS regPerson
LEFT JOIN projectPersonRoles AS regPersonRole ON regPersonRole.projectPersonId = regPerson.id
WHERE projectKey = ? AND role LIKE 'ROLE_%'
ORDER BY name,role
EOD;
        $stmt = $this->regPersonConn->executeQuery($sql,[$projectId]);
        $persons = [];
        while($row = $stmt->fetch())
        {
            $personId = $row['personId'];

            if (!isset($persons[$personId])) {
                $person = [
                    'personId' => $personId,
                    'name'     => $row['name'],
                    'roles'    => $row['role'],
                ];
                $persons[$personId] = $person;
            }
            else {
                $persons[$personId]['roles'] .= ' ' . $row['role'];
            }
        }
        $sql  = 'SELECT personKey AS personId, username FROM users WHERE personKey IN (?) ORDER BY name';
        $stmt = $this->userConn->executeQuery($sql,[array_keys($persons)],[Connection::PARAM_STR_ARRAY]);
        $userChoices = [];
        while($row = $stmt->fetch()) {

            $person = $persons[$row['personId']];

            $userChoices[$row['username']] = $person['name'] . ' ' . $person['roles'];
        }
        return $userChoices;
    }
    public function isApprovedForRole($role,$regPersonId)
    {
        if (!$regPersonId) {
            return false;
        }
        list($projectId,$personId) = explode(':',$regPersonId);

        // TODO: Test some refinements here
        $sql = <<<EOD
SELECT regPersonRole.approved 
FROM projectPersons AS regPerson
LEFT JOIN projectPersonRoles AS regPersonRole ON regPersonRole.projectPersonId = regPerson.id AND regPersonRole.role = ?
WHERE regPerson.projectKey = ? AND regPerson.personKey = ?
EOD;
        $stmt = $this->regPersonConn->executeQuery($sql,[$role,$projectId,$personId]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        return $row['approved'] ? true : false;
    }
    /* ==========================================
     * Just for the referee summary for now but
     * Also a partial design for future RegPerson
     * 
     * @param  $projectId string
     * @return RegPerson[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findRegPersons($projectId)
    {
        $sql = <<<EOD
SELECT
  projectKey AS projectId,
  personKey  AS personId,
  orgKey     AS orgId,
  fedKey     AS fedId,
  regYear    AS regYear,
  name,email,phone,gender,dob,age,shirtSize,notes,notesUser,avail
FROM projectPersons AS regPerson
WHERE regPerson.projectKey = ?
EOD;
        $stmt = $this->regPersonConn->executeQuery($sql,[$projectId]);
        $regPersons = [];
        while($row = $stmt->fetch()) {
            $regPerson = RegPerson::createFromArray($row);
            $regPersons[$regPerson->personId] = $regPerson;
        }
        $sql = <<<EOD
SELECT 
  projectKey AS projectId,
  personKey  AS personId,
  role,badge,approved 
FROM projectPersonRoles  AS regPersonRole 
LEFT JOIN projectPersons AS regPerson ON regPerson.id = regPersonRole.projectPersonId
WHERE projectKey = ?
EOD;
        $stmt = $this->regPersonConn->executeQuery($sql,[$projectId]);
        while($row = $stmt->fetch()) {
            $regPersonRole = RegPersonRole::createFromArray($row);
            $regPersons[$regPersonRole->personId]->addRole($regPersonRole);
        }
        return array_values($regPersons);
    }
}