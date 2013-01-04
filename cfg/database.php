<?php

    // Database configuration
    $config['database']['dbdriver'] = 'mysql';
    $config['database']['hostname'] = 'localhost';
    $config['database']['database'] = 'foolslide';
    $config['database']['username'] = 'foolslide';
    $config['database']['password'] = 'e7Yn8K8mS5PSd3BX';
    $config['database']['dbport']   = '3306';

    // Tables configuration
    $config['links']['fs_comics']['fs_chapters'] = 'id';
    $config['links']['fs_chapters']['fs_comics'] = 'comic_id';
    $config['links']['fs_chapters']['fs_joints'] = 'joint_id';
    $config['links']['fs_chapters']['fs_pages']  = 'id';
    $config['links']['fs_chapters']['fs_teams']  = 'team_id';
    $config['links']['fs_pages']['fs_chapters']  = 'chapter_id';
    $config['links']['fs_teams']['fs_chapters']  = 'id';
    $config['links']['fs_teams']['fs_joints']    = 'id';
    $config['links']['fs_joints']['fs_chapters'] = 'joint_id';
    $config['links']['fs_joints']['fs_teams']    = 'team_id';

    // Tables Fields
    $config['fields']['fs_comics'][]   = 'id';
    $config['fields']['fs_comics'][]   = 'name';
    $config['fields']['fs_comics'][]   = 'stub';
    $config['fields']['fs_comics'][]   = 'uniqid';
    $config['fields']['fs_comics'][]   = 'hidden';
    $config['fields']['fs_comics'][]   = 'description';
    $config['fields']['fs_comics'][]   = 'thumbnail';
    $config['fields']['fs_comics'][]   = 'adult';
    $config['fields']['fs_comics'][]   = 'created';
    $config['fields']['fs_comics'][]   = 'updated';
    $config['fields']['fs_chapters'][] = 'id';
    $config['fields']['fs_chapters'][] = 'chapter';
    $config['fields']['fs_chapters'][] = 'subchapter';
    $config['fields']['fs_chapters'][] = 'volume';
    $config['fields']['fs_chapters'][] = 'name';
    $config['fields']['fs_chapters'][] = 'stub';
    $config['fields']['fs_chapters'][] = 'uniqid';
    $config['fields']['fs_chapters'][] = 'hidden';
    $config['fields']['fs_chapters'][] = 'created';
    $config['fields']['fs_chapters'][] = 'updated';
    $config['fields']['fs_pages'][]    = 'filename';
    $config['fields']['fs_pages'][]    = 'hidden';
    $config['fields']['fs_teams'][]    = 'name';
    $config['fields']['fs_joints'][]   = 'id';

?>
