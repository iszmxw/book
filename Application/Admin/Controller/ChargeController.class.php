<?php

namespace Admin\Controller;

class ChargeController extends AdminController
{
    // 通知列表
    public function index()
    {
        $where           = $this->_get_where();
        $where['status'] = 2;
        $list            = $this->_get_list('charge', $where);
        foreach ($list as $k => $v) {
            $list[$k]['nickname'] = M('user')->where(array('id' => $v['user_id']))->getField('nickname');
        }
        $this->assign($where);
        $this->assign('list', $list);
        $this->assign('page', $this->data['page']);
        $this->display();
    }

    private function _get_where()
    {
        if (IS_POST) {
            $_GET['p'] = 1; //如果是post的话回到第一页
        }
        $user_id = I('user_id');
        $time1   = I('time1');
        $time2   = I('time2');
        $where   = [];
        if (!empty($user_id)) {
            $where['user_id'] = intval($user_id);
        }

        if (!empty($time1) && !empty($time2)) {
            $where['create_time'] = array(
                array('gt', strtotime($time1)),
                array('lt', strtotime($time2) + 86400)
            );
        } elseif (!empty($time1)) {
            $where['create_time'] = array('gt', strtotime($time1));
        } elseif (!empty($time2)) {
            $where['create_time'] = array('lt', strtotime($time2) + 86400);
        }
        return $where;
    }

}

?>