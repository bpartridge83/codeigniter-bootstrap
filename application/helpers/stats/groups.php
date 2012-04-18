<?php

    $groups = array(
        'overall' => array(
            'games',
            'at-bats',
            'runs-scored',
            'hits',
            'batting-average',
            'home-runs',
            'slugging-percentage',
            'rbi',
            'walks',
            'strikeouts',
        ),
        'offense' => array(
            //'games-played',
            //'games-started',
            'at-bats',
            'runs-scored',
            'hits',
            'batting-average',
            'doubles',
            'triples',
            'home-runs',
            'total-bases',
            'slugging-percentage',
            'on-base-percentage',
            'contact-percentage',
            'babip',
            'isolated-power',
            'power-vs-speed',
            'rbi',
            'stolen-bases',
            'stolen-base-attempts',
            'walks',
            'strikeouts',
            'grounded-double-plays',
            'hit-by-pitch',
            'sacrifice-hits',
            'sacrifice-flies'
        ),
        'pitching' => array(
            'appearances',
            'games-started',
            'wins',
            'losses',
            'innings-pitched',
            'era',
            'whip',
            'runs-allowed',
            'complete-games',
            'saves',
            'shutouts',
            'hits-allowed',
            'strikeouts-pitched',
            'walks-allowed',
        ),
        'all' => array(
            'games',
            'gamesStarted',
            'gamesPlayed',
            'at-bats',
            'runs-scored',
            'hits',
            'doubles',
            'triples',
            'home-runs',
            'total-bases',
            'rbi',
            'stolen-bases',
            'stolen-base-attempts',
            'walks',
            'strikeouts',
            'hit-by-pitch',
            'sacrifice-hits',
            'sacrifice-flies',
            'spacer',
            'appearances',
            'wins',
            'losses',
            'innings-pitched',
            'strikeouts-pitched',
            'runs-allowed',
            'complete-games',
            'saves',
            'shutouts',
            'hits-allowed',
            'walks-allowed',
        ),
    );
    
    $stats = array(
        'games' => array(
            'label' => 'G',
            'title' => 'Games Played',
            'rel' => 'twipsy',
            'method' => 'getGames',
            'default' => '0'
        ),
        'games-started' => array(
            'label' => 'GS',
            'title' => 'Games Started',
            'rel' => 'twipsy',
            'method' => 'getGamesStarted',
            'default' => '0'
        ),
        'games-played' => array(
            'label' => 'GP',
            'title' => 'Games Played',
            'rel' => 'twipsy',
            'method' => 'getGamesPlayed',
            'default' => '0'
        ),
        'wins' => array(
            'label' => 'W',
            'title' => 'Wins',
            'rel' => 'twipsy',
            'method' => 'getWins',
            'default' => '0'
        ),
        'losses' => array(
            'label' => 'L',
            'title' => 'Losses',
            'rel' => 'twipsy',
            'method' => 'getLosses',
            'default' => '0'
        ),
        'at-bats' => array(
            'label' => 'AB',
            'title' => 'At-Bats',
            'rel' => 'twipsy',
            'method' => 'getAtBats',
            'default' => '0'
        ),
        'runs-scored' => array(
            'label' => 'R',
            'title' => 'Runs Scored',
            'rel' => 'twipsy',
            'method' => 'getRuns',
            'default' => '0'
        ),
        'hits' => array(
            'label' => 'H',
            'title' => 'Hits',
            'rel' => 'twipsy',
            'method' => 'getHits',
            'default' => '0'
        ),
        'batting-average' => array(
            'label' => 'Avg',
            'title' => 'Batting Average',
            'rel' => 'twipsy',
            'method' => 'getAvg',
            'default' => '0.000'
        ),
        'doubles' => array(
            'label' => '2B',
            'title' => 'Doubles',
            'rel' => 'twipsy',
            'method' => 'getDoubles',
            'default' => '0'
        ),
        'triples' => array(
            'label' => '3B',
            'title' => 'Triples',
            'rel' => 'twipsy',
            'method' => 'getTriples',
            'default' => '0'
        ),
        'home-runs' => array(
            'label' => 'HR',
            'title' => 'Home Runs',
            'rel' => 'twipsy',
            'method' => 'getHomeRuns',
            'default' => '0'
        ),
        'babip' => array(
            'label' => 'BABIP',
            'title' => 'Batting Average on Balls In Play',
            'rel' => 'popover',
            'content' => '(H-HR) / (AB-HR-K+SF)',
            'method' => 'getBabip',
            'default' => '0.000'
        ),
        'contact-percentage' => array(
            'label' => 'C%',
            'title' => 'Contact Percentage',
            'rel' => 'popover',
            'content' => '(AB-K)/AB',
            'method' => 'getContactPercentage',
            'default' => '0.000'
        ),
        'slugging-percentage' => array(
            'label' => 'SLG',
            'title' => 'Slugging Percentage',
            'content' => '(Total Bases / At-Bats)',
            'rel' => 'popover',
            'method' => 'getSluggingPercentage',
            'default' => '0.000'
        ),
        'rbi' => array(
            'label' => 'RBI',
            'title' => 'Runs Batted In',
            'rel' => 'twipsy',
            'method' => 'getRunsBattedIn',
            'default' => '0'
        ),
        'walks' => array(
            'label' => 'BB',
            'title' => 'Walks',
            'rel' => 'twipsy',
            'method' => 'getWalks',
            'default' => '0'
        ),
        'strikeouts' => array(
            'label' => 'SO',
            'title' => 'Strikeouts',
            'rel' => 'twipsy',
            'method' => 'getStrikeouts',
            'default' => '0'
        ),
        'grounded-double-plays' => array(
            'label' => 'GDP',
            'title' => 'Grounded into Double Plays',
            'rel' => 'twipsy',
            'method' => 'getGroundedIntoDoublePlays',
            'default' => '0'
        ),
        'hit-by-pitch' => array(
            'label' => 'HBP',
            'title' => 'Hit-by-Pitch',
            'rel' => 'twipsy',
            'method' => 'getHitByPitch',
            'default' => '0'
        ),
        'sacrifice-hits' => array(
            'label' => 'SH',
            'title' => 'Sacrifice Hits',
            'rel' => 'twipsy',
            'method' => 'getSacrificeHits',
            'default' => '0'
        ),
        'sacrifice-flies' => array(
            'label' => 'SF',
            'title' => 'Sacrifice Flies',
            'rel' => 'twipsy',
            'method' => 'getSacrificeFlies',
            'default' => '0'
        ),
        'total-bases' => array(
            'label' => 'TB',
            'title' => 'Total Bases',
            'rel' => 'twipsy',
            'method' => 'getTotalBases',
            'default' => '0'
        ),
        'stolen-bases' => array(
            'label' => 'SB',
            'title' => 'Stolen Bases',
            'rel' => 'twipsy',
            'method' => 'getStolenBases',
            'default' => '0'
        ),
        'stolen-base-attempts' => array(
            'label' => 'SBA',
            'title' => 'Stolen Base Attempts',
            'rel' => 'twipsy',
            'method' => 'getStolenBaseAttempts',
            'default' => '0'
        ),
        'complete-games' => array(
            'label' => 'CG',
            'title' => 'Complete Games',
            'rel' => 'twipsy',
            'method' => 'getCompleteGames',
            'default' => '0'
        ),
        'saves' => array(
            'label' => 'SV',
            'title' => 'Saves',
            'rel' => 'twipsy',
            'method' => 'getSaves',
            'default' => '0'
        ),
        'shutouts' => array(
            'label' => 'ShO',
            'title' => 'Shutouts',
            'rel' => 'twipsy',
            'method' => 'getShutouts',
            'default' => '0'
        ),
        'innings-pitched' => array(
            'label' => 'IP',
            'title' => 'Innings Pitched',
            'rel' => 'twipsy',
            'method' => 'getInningsPitched',
            'default' => '0'
        ),
        'hits-allowed' => array(
            'label' => 'H',
            'title' => 'Hits Allowed',
            'rel' => 'twipsy',
            'method' => 'getHitsAllowed',
            'default' => '0'
        ),
        'isolated-power' => array(
            'label' => 'ISO',
            'title' => 'Isolated Power',
            'content' => '(2B + (2 x 3B) + (3 x HR)) / At-Bats',
            'rel' => 'popover',
            'method' => 'getISO',
            'default' => '0'
        ),
        'power-vs-speed' => array(
            'label' => 'P/S',
            'title' => 'Power vs Speed',
            'content' => '(2 x HR x SB) / (HR + SB)',
            'rel' => 'popover',
            'method' => 'getPowerVsSpeed',
            'default' => '0.000'
        ),
        'on-base-percentage' => array(
            'label' => 'OBP',
            'title' => 'On-Base Percentage',
            'content' => '(H+BB+HBP) / (AB+BB+HBP+SF)',
            'rel' => 'popover',
            'method' => 'getOnBasePercentage',
            'default' => '0'
        ),
        'runs-allowed' => array(
            'label' => 'R',
            'title' => 'Runs Allowed',
            'rel' => 'twipsy',
            'method' => 'getRunsAllowed',
            'default' => '0'
        ),
        'walks-allowed' => array(
            'label' => 'BB',
            'title' => 'Walks Allowed',
            'rel' => 'twipsy',
            'method' => 'getWalksAllowed',
            'default' => '0'
        ),
        'strikeouts-pitched' => array(
            'label' => 'K',
            'title' => 'Strikeouts Pitched',
            'rel' => 'twipsy',
            'method' => 'getStrikeoutsPitched',
            'default' => '0'
        ),
        'era' => array(
            'label' => 'ERA',
            'title' => 'Earned Run Average',
            'rel' => 'twipsy',
            'method' => 'getEra',
            'default' => '0.00'
        ),
        'appearances' => array(
            'label' => 'APP',
            'title' => 'Apperances',
            'rel' => 'twipsy',
            'method' => 'getAppearances',
            'default' => '0'
        ),
        'whip' => array(
            'label' => 'WHIP',
            'title' => 'Walks Plus Hits per Innings Pitched',
            'rel' => 'twipsy',
            'method' => 'getWHIP',
            'default' => '0.00'
        ),
        'year' => array(
            'label' => 'Year',
            'title' => 'Year',
            'rel' => 'twipsy',
            'method' => 'getYear',
            'default' => '-'
        ),
        'spacer' => array(
            'label' => '|',
            'default' => '|'
        ),
    );

