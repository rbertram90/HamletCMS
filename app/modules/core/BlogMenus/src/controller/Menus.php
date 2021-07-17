<?php

namespace HamletCMS\BlogMenus\controller;

use HamletCMS\HamletCMS;
use HamletCMS\GenericController;

class Menus extends GenericController {

    public function __construct()
    {
        parent::__construct();

        $this->model = HamletCMS::model('\HamletCMS\BlogMenus\model\Menus');
        $this->modelItems = HamletCMS::model('\HamletCMS\BlogMenus\model\MenuItems');

        $this->blog = HamletCMS::getActiveBlog();
        $this->response->setVar('blog', $this->blog);
    }

    /**
     * Route: /cms/menus/manage/{BLOG_ID}
     */
    public function listMenus()
    {
        $this->response->setTitle('Manage menus - ' . $this->blog->name);
        $this->response->setVar('menus', $this->model->get('*', ['blog_id' => $this->blog->id]));
        $this->response->write('allMenus.tpl', 'BlogMenus');
    }

    /**
     * Route: /cms/menus/create/{BLOG_ID}
     */
    public function createMenu()
    {
        if ($this->request->method() != 'POST') return;

        $menuName = $this->request->getString('menu_name', false);
        $managePath = '/cms/menus/manage/'. $this->blog->id;

        if (!$menuName || strlen($menuName) == 0) {
            $this->response->redirect($managePath, 'Please enter the menu name', 'error');
        }

        if ($this->model->insert(['name' => $menuName, 'blog_id' => $this->blog->id])) {
            $this->response->redirect($managePath, 'Menu created', 'success');
        }
        else {
            $this->response->redirect($managePath, 'Failed to create menu', 'error');
        }

    }

    /**
     * Route: /cms/menus/delete/{BLOG_ID}/{MENU_ID}
     */
    public function deleteMenu()
    {
        $menu = $this->requireMenuFromRequest();
        
        // Run delete
        $delete = $this->model->delete(['id' => $menu->id]);

        if ($delete) {
            $this->response->redirect('/cms/menus/manage/'. $this->blog->id, 'Menu deleted', 'success');
        }
        else {
            $this->response->redirect($errorRedirectUrl, 'Unable to delete link', 'error');
        }
    }

    /**
     * Route: /cms/menus/edit/{BLOG_ID}/{MENU_ID}
     */
    public function editLinks()
    {
        $menu = $this->requireMenuFromRequest();

        if ($this->request->method() == 'POST') return $this->saveMenu($menu);

        $this->response->setTitle('Manage menu items - '. $menu->name);
        $this->response->setVar('menu', $menu);
        $this->response->write('editLinks.tpl', 'BlogMenus');
    }

    /**
     * Route: /cms/menus/movelinkdown/{BLOG_ID}/{LINK_ID}
     */
    public function moveLinkDown()
    {
        $link = $this->requireMenuLinkFromRequest();
        $menu = $link->menu();
        $redirect = "/cms/menus/edit/{$this->blog->id}/{$menu->id}";
        
        if (!$menu->blog_id == $this->blog->id) {
            $this->response->redirect($redirect, 'Blog mismatch', 'error');
        }

        $menuItems = $menu->items();

        if ($link->weight >= count($menuItems)) {
            $this->response->redirect($redirect, 'Link cannot be moved down', 'error');
        }

        // Move up the item below
        $update1 = $this->modelItems->update(['weight' => $link->weight + 1, 'menu_id' => $menu->id], ['weight' => $link->weight]);

        // Move down this item
        $update2 = $this->modelItems->update(['id' => $link->id], ['weight' => $link->weight + 1]);

        if ($update1 && $update2) {
            $this->response->redirect($redirect, 'Link order changed', 'success');
        }
        else {
            $this->response->redirect($redirect, 'Unable to move link down', 'error');
        }
    }

    /**
     * Route: /cms/menus/movelinkup/{BLOG_ID}/{LINK_ID}
     */
    public function moveLinkUp()
    {
        $link = $this->requireMenuLinkFromRequest();
        $menu = $link->menu();
        $redirect = "/cms/menus/edit/{$this->blog->id}/{$menu->id}";
        
        if (!$menu->blog_id == $this->blog->id) {
            $this->response->redirect($redirect, 'Blog mismatch', 'error');
        }

        $menuItems = $menu->items();

        if ($link->weight <= 1) {
            $this->response->redirect($redirect, 'Link cannot be moved up', 'error');
        }

        // Move up the item below
        $update1 = $this->modelItems->update(['weight' => $link->weight - 1, 'menu_id' => $menu->id], ['weight' => $link->weight]);

        // Move down this item
        $update2 = $this->modelItems->update(['id' => $link->id], ['weight' => $link->weight - 1]);

        if ($update1 && $update2) {
            $this->response->redirect($redirect, 'Link order changed', 'success');
        }
        else {
            $this->response->redirect($redirect, 'Unable to move link up', 'error');
        }
    }

    /**
     * Save options for menu
     */
    protected function saveMenu($menu)
    {
        $name = $this->request->getString('name');
        $sort = $this->request->getString('sort');

        if ($sort != 'custom') $sort = 'name';

        $redirect = "/cms/menus/edit/{$this->blog->id}/{$menu->id}";

        if (strlen($name) == 0) {
            $this->response->redirect($redirect, 'Name field is required', 'error');
        }

        if ($this->model->update(['id' => $menu->id], ['name' => $name, 'sort' => $sort])) {
            $this->response->redirect($redirect, 'Menu updated', 'success');
        }
        else {
            $this->response->redirect($redirect, 'Error when saving menu', 'error');
        }
    }

    /**
     * Route: /cms/menus/addlink/{BLOG_ID}/{MENU_ID}
     */
    public function createLink()
    {
        $menu = $this->requireMenuFromRequest();

        if ($this->request->method() == 'POST') return $this->processCreateLink($menu);
        
        $this->response->setTitle('Add menu item - '. $menu->name);
        $this->response->setVar('menu', $menu);
        $postsModel = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
        $this->response->setVar('tags', $postsModel->getAllTagsByBlog($this->blog->id));
        $this->response->write('addLink.tpl', 'BlogMenus');
    }

    /**
     * Insert link data to database
     */
    protected function processCreateLink($menu)
    {
        $type = $this->request->getString('type');
        $text = $this->request->getString('text', false);
        $target = false;

        $errorRedirectUrl = '/cms/menus/addlink/'. $this->blog->id .'/'. $menu->id;
        if (!$text || strlen($text) == 0) $this->response->redirect($errorRedirectUrl, 'Link text required', 'error');

        $target = $this->getLinkTargetFromRequest();
        if ($target == false) {
            $this->response->redirect($errorRedirectUrl, 'Could not verify link target', 'error');
        }

        $insert = $this->modelItems->insert([
            'type' => $type,
            'text' => $text,
            'link_target' => $target,
            'menu_id' => $menu->id,
            'weight' => count($menu->items()) + 1, // always add last
            'new_window' => $this->request->getString('new_window', 'off') == 'on'
        ]);

        if ($insert) {
            $this->response->redirect('/cms/menus/edit/'. $this->blog->id .'/'. $menu->id, 'Link created', 'success');
        }
        else {
            $this->response->redirect($errorRedirectUrl, 'Unable to create link', 'error');
        }
    }

    /**
     * Route: /cms/menus/editlink/{BLOG_ID}/{LINK_ID}
     */
    public function editLink()
    {
        $link = $this->requireMenuLinkFromRequest();

        if ($this->request->method() == 'POST') return $this->processUpdateLink($link);
        
        $this->response->setTitle('Edit item - '. $link->text);
        $this->response->setVar('menu', $link->menu());
        $this->response->setVar('link', $link);
        $postsModel = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
        $this->response->setVar('tags', $postsModel->getAllTagsByBlog($this->blog->id));
        $this->response->write('addLink.tpl', 'BlogMenus');
    }

    /**
     * Update link data in database
     */
    protected function processUpdateLink($link)
    {
        $type = $this->request->getString('type');
        $text = $this->request->getString('text', false);
        $target = false;

        $errorRedirectUrl = '/cms/menus/editlink/'. $this->blog->id .'/'. $link->id;
        if (!$text || strlen($text) == 0) $this->response->redirect($errorRedirectUrl, 'Link text required', 'error');

        $target = $this->getLinkTargetFromRequest();
        if ($target == false) {
            $this->response->redirect($errorRedirectUrl, 'Could not verify link target', 'error');
        }

        $update = $this->modelItems->update(['id' => $link->id], [
            'type' => $type,
            'text' => $text,
            'link_target' => $target,
            'new_window' => $this->request->getString('new_window', 'off') == 'on'
        ]);

        if ($update) {
            $menu = $link->menu();
            $this->response->redirect('/cms/menus/edit/'. $this->blog->id .'/'. $menu->id, 'Link updated', 'success');
        }
        else {
            $this->response->redirect($errorRedirectUrl, 'Unable to update link', 'error');
        }
    }

    /**
     * Route: /cms/menus/deletelink/{BLOG_ID}/{LINK_ID}
     */
    public function deleteLink()
    {
        $link = $this->requireMenuLinkFromRequest();
        $menu = $link->menu();
        $errorRedirectUrl = '/cms/menus/edit/'. $this->blog->id .'/'. $menu->id;

        // Run delete
        if ($this->modelItems->delete(['id' => $link->id])) {
            $this->modelItems->reWeightLinks($link);
            $this->response->redirect('/cms/menus/edit/'. $this->blog->id .'/'. $menu->id, 'Link deleted', 'success');
        }
        else {
            $this->response->redirect($errorRedirectUrl, 'Unable to delete link', 'error');
        }
    }

    /**
     * Get a menu item using the ID passed in the URL
     */
    protected function requireMenuLinkFromRequest()
    {
        $linkID = $this->request->getUrlParameter(2);

        if (is_numeric($linkID) && $linkID > 0) $link = $this->modelItems->getItemById($linkID);
        if (!isset($link)) $this->response->redirect('/cms', 'Could not find link', 'error');

        return $link;
    }

    /**
     * Get the menu using the ID passed in the URL
     * 
     * @return \HamletCMS\BlogMenus\BlogMenu|null
     */
    protected function requireMenuFromRequest()
    {
        $menu = null;
        $menuID = $this->request->getUrlParameter(2);

        if (is_numeric($menuID) && $menuID > 0) $menu = $this->model->getMenuById($menuID);
        if (is_null($menu)) $this->response->redirect('/cms', 'Could not find menu', 'error');

        return $menu;
    }

    /**
     * Common between add and edit link
     * 
     * @return string
     */
    protected function getLinkTargetFromRequest()
    {
        $target = false;
        $type = $this->request->getString('type');

        switch ($type) {
            case 'post':
                $postsModel = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
                $postID = $this->request->getInt('post_id', 0);
                if ($post = $postsModel->getPostById($postID)) $target = $postID;
                break;
            case 'blog':
                $blogsModel = HamletCMS::model('\HamletCMS\Blog\model\Blogs');
                $blogID = $this->request->getInt('blog_id', 0);
                if ($blog = $blogsModel->getBlogById($blogID)) $target = $blogID;
                break;
            case 'tag':
                $target = $this->request->getString('tag', false);
                break;
            case 'mail':
            case 'tel':
            case 'external':
                $target = $this->request->getString('target', false);
                break;
        }

        return $target;
    }

}