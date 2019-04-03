games
  gameId      = generated projectId:gameNumber
  projectId   = AYSONationalGames2016
  gameNumber  = unique in project integer
  role        = game
  fieldName   = unique in project field name
  venueName   = unique in project venue name
  start       = datetime
  finish      = datetime
  state       = Always published
  status      = normal, played, sometimes blank
  reportText  = probably has report stuff
  reportState = pending, initial, submitted, verified
  selfAssign  = 0 or 1, not sure it is used
  
There is nothing in the game itself for gender, division pool play etc
gameTeams
  gameTeamId    projectId:gameId:slot
  projectId
  gameId
  gameNumber
  slot          0 for home, 1 for away
  poolTeamId
  results       boolean
  resultsDetail vachar(40)?
  
  pointsScored    integer probably should be goalsScored
  pointsAllowed
  pointsEarned
  pointsDeducted
  sportsmanship   integer
  injuries
  misconduct      longtext probably serialized
  
poolTeams             regTeams
  poolTeamId            regTeamId    AYSONationalGames2016:U10BCore01
  projectId             projectId    AYSONationalGames2016
  poolKey               teamKey      U10BCore01 
  poolTypeKey           teamNumber - 1 to 24, given on registration
  poolTeamKey           teamName     #01 01-C-0002-CA Gould
  poolView              teamPoints - soccerfest
  poolSlotView          orgId        AYSOR:0002 
  poolTypePool          orgView      01-C-0002-CA
  poolTeamView
  poolTeamSlotView
  sourcePoolKeys
  sourcePoolSlots
  program               program      Core
  gender                gender       B
  age                   age          U10
  division              division     U10B
  regTeamId             regTeamId
  regTeamName           teamName
  regTeamPoints         teamPoints
  
