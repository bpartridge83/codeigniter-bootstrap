<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* --------------------------------------------------------------
 * Teams
 * ------------------------------------------------------------ */

$route['teams_index'] = array(
    'pattern' => 'teams',
    'action' => 'teams:index',
);

$route['team_merge'] = array(
    'pattern' => 'teams/merge/{id}',
    'parameters' => array(
        'id' => ':any'
    ),
    'action' => 'teams:merge',
);

$route['team_edit_seasons'] = array(
    'pattern' => 'team/{slug}/edit/seasons',
    'parameters' => array(
        'slug' => ':any'
    ),
    'action' => 'teams:editSeasons',
);

$route['team_edit'] = array(
    'pattern' => 'team/{slug}/edit',
    'parameters' => array(
        'slug' => ':any'
    ),
    'action' => 'teams:edit',
);

$route['team_view_season'] = array(
    'pattern' => 'team/{slug}/{year}/players',
    'parameters' => array(
        'slug' => ':any',
        'year' => ':num'
    ),
    'action' => 'teams:viewSeasonPlayers',
);

$route['team_view_season_pitching'] = array(
    'pattern' => 'team/{slug}/{year}/pitching',
    'parameters' => array(
        'slug' => ':any',
        'year' => ':num'
    ),
    'action' => 'teams:viewSeasonPitching',
);

$route['team_view_season_games'] = array(
    'pattern' => 'team/{slug}/{year}/games',
    'parameters' => array(
        'slug' => ':any',
        'year' => ':num'
    ),
    'action' => 'teams:viewSeasonGameLog',
);

$route['teams_view_division'] = array(
    'pattern' => 'teams/{division}',
    'parameters' => array(
        'division' => ':any'
    ),
    'action' => 'teams:viewDivision',
);

$route['team_view'] = array(
    'pattern' => 'team/{slug}',
    'parameters' => array(
        'slug' => ':any'
    ),
    'action' => 'teams:view',
);

