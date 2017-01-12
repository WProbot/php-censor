<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Controller;

use b8;
use b8\Form;
use b8\Store;
use PHPCensor\Controller;
use PHPCensor\Model\ProjectGroup;
use PHPCensor\Helper\Lang;

/**
 * Project Controller - Allows users to create, edit and view projects.
 * @author       Dan Cryer <dan@block8.co.uk>
 * @package      PHPCI
 * @subpackage   Web
 */
class GroupController extends Controller
{
    /**
     * @var \PHPCensor\Store\ProjectGroupStore
     */
    protected $groupStore;

    /**
     * Set up this controller.
     */
    public function init()
    {
        $this->groupStore = b8\Store\Factory::getStore('ProjectGroup');
    }

    /**
     * List project groups.
     */
    public function index()
    {
        $this->requireAdmin();

        $groups    = [];
        $groupList = $this->groupStore->getWhere([], 100, 0, [], ['title' => 'ASC']);

        foreach ($groupList['items'] as $group) {
            $thisGroup = [
                'title' => $group->getTitle(),
                'id'    => $group->getId(),
            ];
            $projects = b8\Store\Factory::getStore('Project')->getByGroupId($group->getId());
            $thisGroup['projects'] = $projects['items'];
            $groups[] = $thisGroup;
        }

        $this->view->groups = $groups;
    }

    /**
     * Add or edit a project group.
     * @param null $groupId
     * @return void|b8\Http\Response\RedirectResponse
     */
    public function edit($groupId = null)
    {
        $this->requireAdmin();

        if (!is_null($groupId)) {
            $group = $this->groupStore->getById($groupId);
        } else {
            $group = new ProjectGroup();
        }

        if ($this->request->getMethod() == 'POST') {
            $group->setTitle($this->getParam('title'));
            $this->groupStore->save($group);

            $response = new b8\Http\Response\RedirectResponse();
            $response->setHeader('Location', APP_URL.'group');
            return $response;
        }

        $form = new Form();
        $form->setMethod('POST');
        $form->setAction(APP_URL . 'group/edit' . (!is_null($groupId) ? '/' . $groupId : ''));

        $title = new Form\Element\Text('title');
        $title->setContainerClass('form-group');
        $title->setClass('form-control');
        $title->setLabel(Lang::get('group_title'));
        $title->setValue($group->getTitle());

        $submit = new Form\Element\Submit();
        $submit->setClass('btn btn-success');
        $submit->setValue(Lang::get('group_save'));

        $form->addField($title);
        $form->addField($submit);

        $this->view->form = $form;
    }

    /**
     * Delete a project group.
     * @param $groupId
     * @return b8\Http\Response\RedirectResponse
     */
    public function delete($groupId)
    {
        $this->requireAdmin();
        $group = $this->groupStore->getById($groupId);

        $this->groupStore->delete($group);
        $response = new b8\Http\Response\RedirectResponse();
        $response->setHeader('Location', APP_URL.'group');
        return $response;
    }
}
