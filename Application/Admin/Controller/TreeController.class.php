<?php

namespace Admin\Controller;

class TreeController extends AdminController
{
    // 通知列表
    public function index()
    {
        $this->display();
    }

    // 获取数据
    public function get_users()
    {
        $id   = intval(I('id'));
        $root = I('root');
        if (!$id && $root > 0) {
            $id = intval($root);
        }
        $users = M('user')->where('parent1=' . $id)->select();
        $data  = array();
        foreach ($users as $v) {
            $data[] = array(
                'id'       => $v['id'],
                'name'     => $v['nickname'],
                'url'      => U('User/detail?id=' . $v['id']),
                'isParent' => $v['agent1'] > 0
            );
        }
        $this->ajaxReturn($data);
    }
}