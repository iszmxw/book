<?php

namespace Admin\Controller;

class UserController extends AdminController
{
    /**
     * 用户管理列表
     * @author: iszmxw <mail@54zm.com>
     * @Date：2019/12/14 15:46
     */
    public function index()
    {
        // 接收数据
        $where = [];
        $order = null;
        $id    = I('id');
        $vip   = I('vip');
        $name  = I('name');
        $order = I('order');
        $type  = I('type');
        $data  = [
            'id'    => $id,
            'vip'   => $vip,
            'name'  => $name,
            'order' => $order,
            // 发送的升级模板消息
            'tpls'  => M('tpl')->order('id desc')->select()
        ];
        if ($id) {
            $where['id'] = intval($id);
        }
        if ($vip) {
            $where['vip'] = intval($vip);
        }
        if ($vip == -1) {
            $where['vip'] = 0;
        }
        if ($name) {
            $where['true_name|nickname'] = array('like', '%' . $name . '%');
        }
        // 组合排序方式
        if (in_array($order, array('id', 'money', 'agent1', 'agent2', 'agent3', 'sub_time'))) {
            $type  = $type == 'asc' ? 'asc' : 'desc';
            $order = $order . ' ' . $type;
        }
        // 渲染数据到视图
        $this->views($data);
        // 分页获取数据返回到视图
        $this->_list('user', $where, $order);
    }

    /**
     * 用户详细信息
     * @author: iszmxw <mail@54zm.com>
     * @Date：2019/12/14 15:46
     */
    public function detail()
    {
        $id   = intval(I('id'));
        $info = M('user')->find($id);
        $this->assign('info', $info);

        // 查询上级信息
        if ($info['parent1']) {
            $this->assign('parent', M('user')->find($info['parent1']));
        }

        // 查询分成总额
        $separate_money  = M('separate_log')->where('user_id=' . $info['id'])->sum('money');
        $this->assign('separate_money', $separate_money);
        $this->display();
    }

    // 赠送分红
    public function reward()
    {
        $user_id = intval($_POST['user_id']);
        $money   = floatval($_POST['money']);
        $type    = intval($_POST['type']);

        if ($user_id < 1 || $money <= 0) {
            $this->error('操作错误');
        }

        $data = array(
            'expense_avail' => array('exp', 'expense_avail+' . $money)
        );
        if ($type == 1) {
            $data['reward'] = array('exp', 'reward+' . $money);
            $tips           = "绩效分红";
        } else {
            $data['reward_global'] = array('exp', 'reward_global+' . $money);
            $tips                  = "全球分红";
        }
        $rs = M('user')->where('id=' . $user_id)->save($data);
        flog($user_id, 'expense', $money, $tips); // 财务记录

        if ($rs) {
            $this->success('操作成功！');
            exit;
        }
    }

    // 校准下级代理数
    public function correct_agent()
    {
        $user_id = intval(I('id'));
        $agent1  = M('user')->where('parent1=' . $user_id)->count();
        $agent2  = M('user')->where('parent2=' . $user_id)->count();
        $agent3  = M('user')->where('parent3=' . $user_id)->count();
        M('user')->where('id=' . $user_id)->save(array(
            'agent1' => $agent1,
            'agent2' => $agent2,
            'agent3' => $agent3
        ));

        $this->success('操作成功！', $_SERVER['HTTP_REFERER']);
    }

    // 充值/扣除
    public function charge()
    {
        $method  = $_POST['method'];
        $money   = $_POST['money'];
        $user_id = intval($_POST['user_id']);

        if ($method == 1) {
            M('user')->where('id=' . $user_id)->save(array(
                'money' => array('exp', 'money+' . $money)
            ));
            flog($user_id, 'money', $money, 15);
        } elseif ($method == 2) {
            M('user')->where('id=' . $user_id)->save(array(
                'money' => array('exp', 'money-' . $money)
            ));
            flog($user_id, 'money', $money, 16);
        }

        $this->success('操作成功');
    }


    public function sendMsg()
    {
        if (IS_POST) {
            $ids   = I('post.ids');
            $users = M('user')->where(array('id' => array('in', $ids)))->select();
            $tpl   = M('tpl')->find(intval($_POST['tid']));
            if ($users) {
                foreach ($users as $k => $v) {
                    $v['mobile'] = $this->_site['mobile'];
                    $v['name']   = $v['nickname'];
                    $v['title']  = $tpl['title'];
                    $v['remark'] = $tpl['remark'];
                    $v['url']    = $tpl['url'];
                    // 发送模板消息通知
                    $tplmsg = new \Common\Util\tplmsg;
                    $tplmsg->goup($v['openid'], $v);
                }

                $this->success('发送成功');
            }
        }
    }

    //设置金额
    public function setMoney()
    {
        $post = I('post.');
        M('user')->where(array('id' => $post['user_id']))->save(array(
            'money' => array('exp', 'money+' . $post['money']),
        ));
        $this->success('充值成功');
    }


    //设置vip
    public function setVip()
    {
        $get    = I('get.');
        $user   = M('user')->find(intval($get['id']));
        $s_time = time();
        $e_time = strtotime("+1 year");

        if ($user['vip'] == 1) {
            M('user')->where(array('id' => $get['id']))->save(array(
                'vip'        => 0,
                "vip_s_time" => 0,
                "vip_e_time" => 0,
            ));
        } else {
            M('user')->where(array('id' => $get['id']))->save(array(
                'vip'        => 1,
                "vip_s_time" => $s_time,
                "vip_e_time" => $e_time,
            ));
        }

        $this->success('设置成功');
    }


    // 到处excel
    public function export()
    {
        if (IS_POST) {
            $_GET = $_REQUEST;
        }
        if (('id')) {
            $where['id'] = intval(I('id'));
        }
        if (I('name')) {
            $where['true_name|nickname'] = array('like', '%' . I('name') . '%');
        }

        $list = M('user')->where($where)->select();
        // 表头
        $data[0] = array(
            '编号',
            '昵称',
            '账户书币',
            '账户余额',
            '电话',
            '性别',
            '是否关注',
            '进入系统时间',
            '是否包年',
            '包年截止时间',
        );
        foreach ($list as $v) {
            if ($v['sex'] == 1) {
                $sex = "男";
            } else {
                $sex = "女";
            }
            if ($v['subscribe'] == 1) {
                $subscribe = "是";
            } else {
                $subscribe = "否";
            }
            if ($v['vip'] == 1) {
                $vip = "是";
            } else {
                $vip = "否";
            }
            if ($v['sub_time']) {
                $sub_time = date('Y-m-d H:i:s', $v['sub_time']);
            } else {
                $sub_time = "无";
            }
            if ($v['vip_e_time']) {
                $time = date('Y-m-d H:i:s', $v['vip_e_time']);
            } else {
                $time = "无";
            }
            $data[] = array(
                $v['id'],
                $v['nickname'],
                $v['money'],
                $v['rmb'],
                $v['mobile'],
                $sex,
                $subscribe,
                $sub_time,
                $vip,
                $time,
            );
        }

        $filename = NOW_TIME . ".csv";
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="' . $filename . '"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");

        foreach ($data as $d) {
            echo implode(',', $d);
            echo "\r\n";
        }

        die();
    }

}

?>