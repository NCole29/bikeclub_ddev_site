<?php

/**
 * @file
 * Install menulinks for report uploads.
 */

 use Symfony\Component\Yaml\Yaml;
 use Drupal\menu_link_content\Entity\MenuLinkContent;

function report_menulinks() {

  // Menu item descriptions
  $minutes = 'Upload Board Meeting Minutes. Permission is granted to Site Administrator and Clerk.';
  $reports = 'Upload Club Reports. Permission is granted to Site Adminitrator and Clerk.';

  $items = [
    ['admin',-40,'Board minutes','/form/board-meeting-minutes',$minutes],
    ['admin',-35,'Club reports','/form/club-reports',$reports],
  ];
  foreach($items as $item) {
    $menu_link = MenuLinkContent::create([
      'menu_name' => $item[0],
      'weight' => $item[1],
      'title' => $item[2],
      'link' => [ 'uri' => 'internal:' . $item[3] ],
      'description' => $item[4],
      'parent' => 'bikeclub_report.admin_uploads',
      'expanded'  => TRUE,
      ]);
    $menu_link->save();
  }
}