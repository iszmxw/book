<?php

namespace Admin\Controller;
/**
 * 用户树形关系控制器
 * Class TreeController
 * @package Admin\Controller
 */
class TreeController extends AdminController
{
    /**
     * 用户树形关系
     * @author: iszmxw <mail@54zm.com>
     * @Date：2019/12/14 16:34
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 获取数据
     * @author: iszmxw <mail@54zm.com>
     * @Date：2019/12/14 16:33
     */
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