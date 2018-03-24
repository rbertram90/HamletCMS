<?php
/**
 * getCMSSideMenu
 * 
 * @param int $blogid
 *   ID for a blog record to match database
 * @param bool $admin
 *   Are we to include the administrator links
 * @param string|null
 *   Key for the menu item to show as active
 * 
 * @return string
 *   HTML to show in side menu with links specific to the blog
 * 
 * @todo not show the settings menu option to users who do not have permission to perform the actions
 */
function getCMSSideMenu($blogid=0, $admin=false, $activeItem=null)
{
    $output = "";
    $links = [
        [
            'key' => 'dashboard',
            'url' => '/',
            'icon' => 'list ul',
            'label' => 'My Blogs',
        ],
    ];

    if ($blogid > 0) {
        $links = array_merge($links, [
            [
                'label' => 'Blog Actions'
            ],
            [
                'key' => 'overview',
                'url' => '/blog/overview/'. $blogid,
                'icon' => 'chart bar',
                'label' => 'Dashboard',
            ],
            [
                'key' => 'posts',
                'url' => '/posts/manage/'. $blogid,
                'icon' => 'copy outline',
                'label' => 'Posts',
            ],
            [
                'key' => 'comments',
                'url' => '/comments/all/'. $blogid,
                'icon' => 'comments outline',
                'label' => 'Comments',
            ],
            [
                'key' => 'files',
                'url' => '/files/manage/'. $blogid,
                'icon' => 'image outline',
                'label' => 'Files',
            ],
        ]);

        if ($admin) {
            $links = array_merge($links, [
                [
                'key' => 'settings',
                'url' => '/settings/menu/'. $blogid,
                'icon' => 'cogs',
                'label' => 'Settings',
                ],
                [
                    'key' => 'users',
                    'url' => '/contributors/manage/'. $blogid,
                    'icon' => 'users',
                    'label' => 'Contributors',
                ]
            ]);
        }

        $links[] = [
            'key' => 'blog',
            'url' => '/blogs/'. $blogid,
            'icon' => 'book',
            'label' => 'View Blog',
        ];
    }

    $links = array_merge($links, [
        [
            'label' => 'Your Account'
        ],
        [
            'key' => 'profile',
            'url' => '/account/user',
            'icon' => 'user',
            'label' => 'View Profile',
        ],
        [
            'key' => 'profile',
            'url' => '/account/settings',
            'icon' => 'cogs',
            'label' => 'Settings',
        ],
        [
            'key' => 'logout',
            'url' => '/account/logout',
            'icon' => 'arrow left',
            'label' => 'Logout',
        ]
    ]);

    foreach ($links as $link) {
        if (isset($link['url'])) {
            $active = $activeItem && $link['key'] == $activeItem ? 'active ' : '';
            $output.= '<a href="'.$link['url'].'" class="' . $active . 'teal item"><span class="left floated"><i class="'.$link['icon'].' icon"></i></span> '.$link['label'].'</a>';
        }
        else {
            $output.= '<div class="header item">' . $link['label'] . '</div>';
        }
    }
    return $output;
}

