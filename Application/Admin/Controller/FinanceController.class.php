<?php

namespace Admin\Controller;


class FinanceController extends AdminController
{

    // 企业转账列表
    public function mch_pay_log()
    {
        if (IS_POST) {
            $_GET = $_REQUEST;
        }

        if (!empty($_GET['status'])) {
            $where['status'] = intval($_GET['status']);
        }
        if (!empty($_GET['user_id'])) {
            $where['user_id'] = intval($_GET['user_id']);
        }
        if (!empty($_GET['time1']) && !empty($_GET['time2'])) {
            $where['create_time'] = array(
                array('gt', strtotime($_GET['time1'])),
                array('lt', strtotime($_GET['time2']) + 86400)
            );
        } elseif (!empty($_GET['time1'])) {
            $where['create_time'] = array('gt', strtotime($_GET['time1']));
        } elseif (!empty($_GET['time2'])) {
            $where['create_time'] = array('lt', strtotime($_GET['time2']) + 86400);
        }
        $this->_list('mch_pay', $where);
    }

    // 给用户付款
    public function pay()
    {
        $user_id   = intval($_GET['user_id']);
        $user_info = M('user')->find($user_id);
        if (IS_POST) {
            $order_id = $user_id . time();

            $data = array(
                'order_id'    => $order_id,
                'user_id'     => $user_id,
                'openid'      => $user_info['openid'],
                'nickname'    => $user_info['nickname'],
                'money'       => intval($_POST['money'] * 100),
                'remark'      => $_POST['remark'],
                'status'      => 1,
                'op'          => session('admin'),
                'create_time' => NOW_TIME
            );

            $id = M('mch_pay')->add($data);
            if (!$id) {
                $this->error('支付失败！');
            }

            $param = array(
                'mch_appid'        => $this->_mp['appid'],
                'mchid'            => $this->_mp['mch_id'],
                'partner_trade_no' => $order_id,
                'openid'           => $user_info['openid'],
                'check_name'       => 'NO_CHECK', // 不验证名字
                //'re_user_name' => '',
                'amount'           => intval($_POST['money'] * 100), // 金额，分
                'desc'             => $_POST['remark'],
            );

            $dd = new \Common\Util\ddwechat;
//            $dd->setParam($this->_mp);
            $ssl = array(
                'sslcert' => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . $this->_mp['cert'] . 'apiclient_cert.pem',
                'sslkey'  => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . $this->_mp['cert'] . 'apiclient_key.pem',
            );
            $rt  = $dd->mch_pay($param, $ssl);
            if ($rt['return_code'] == 'SUCCESS' && $rt['result_code'] == 'SUCCESS') {
                M('mch_pay')->where('id=' . $id)->save(array(
                    'status'     => 2,
                    'payment_no' => $rt['payment_no'],
                    'msg'        => '支付成功'
                ));
                $this->success('支付成功！');
                exit;
            } else {
                M('mch_pay')->where('id=' . $id)->save(array(
                    'status' => -1,
                    'msg'    => $rt['err_code'] . $rt['err_code_des']
                ));

                $this->error('支付失败:' . $rt['err_code_des']);
            }
        }

        $this->assign('user_info', $user_info);
        $this->display();
    }

    // 转币记录
    public function deposit_log()
    {
        if (IS_POST) {
            $_GET = $_REQUEST;
        }


        if (!empty($_GET['deposit_user'])) {
            $where['deposit_user'] = intval($_GET['deposit_user']);
        }
        if (!empty($_GET['accept_user'])) {
            $where['accept_user'] = intval($_GET['accept_user']);
        }
        if (!empty($_GET['time1']) && !empty($_GET['time2'])) {
            $where['create_time'] = array(
                array('gt', strtotime($_GET['time1'])),
                array('lt', strtotime($_GET['time2']) + 86400)
            );
        } elseif (!empty($_GET['time1'])) {
            $where['create_time'] = array('gt', strtotime($_GET['time1']));
        } elseif (!empty($_GET['time2'])) {
            $where['create_time'] = array('lt', strtotime($_GET['time2']) + 86400);
        }
        $this->_list('deposit', $where);
    }

    // 分成记录
    public function separate_log()
    {
        $where = [];
        if (IS_POST) {
            $_GET['p'] = 1;
        }
        $data = [
            'status'   => I('status'),
            'user_id'  => I('user_id'),
            'order_id' => I('order_id'),
            'order_sn' => I('order_sn'),
            'level'    => I('level'),
            'time1'    => I('time1'),
            'time2'    => I('time2'),
        ];
        if (I('status')) {
            $where['status'] = intval(I('status'));
        }
        if (I('user_id')) {
            $where['user_id'] = intval(I('user_id'));
        }
        if (I('order_id')) {
            $where['order_id'] = intval(I('order_id'));
        }
        if (I('order_sn')) {
            $where['order_sn'] = intval(I('order_sn'));
        }
        if (I('level')) {
            $where['level'] = intval(I('level'));
        }
        if (I('time1') && I('time2')) {
            $where['create_time'] = array(
                array('gt', strtotime(I('time1')),
                    array('lt', strtotime(I('time2')) + 86400)
                ));
        } elseif (I('time1')) {
            $where['create_time'] = array('gt', strtotime(I('time1')));
        } elseif (I('time2')) {
            $where['create_time'] = array('lt', strtotime(I('time2')) + 86400);
        }
        $this->assign($data);
        $this->_list('separate_log', $where);
    }

    // 帐户变动记录
    public function finance_log()
    {
        $where            = [];
        $data['action']   = I('action');
        $data['user_id']  = I('user_id');
        $data['order_sn'] = I('order_sn');
        $data['type']     = I('type');
        $data['time1']    = I('time1');
        $data['time2']    = I('time2');
        if (I('action')) {
            $where['action'] = intval(I('action'));
        }
        if (I('user_id')) {
            $where['user_id'] = intval(I('user_id'));
        }
        if (I('order_sn')) {
            $where['order_sn'] = intval(I('order_sn'));
        }
        if (I('type')) {
            $where['type'] = I('type');
        }

        if (I('time1') && I('time2')) {
            $where['create_time'] = array(
                array('gt', strtotime(I('time1'))),
                array('lt', strtotime(I('time2')) + 86400)
            );
        } elseif (I('time1')) {
            $where['create_time'] = array('gt', strtotime(I('time1')));
        } elseif (I('time2')) {
            $where['create_time'] = array('lt', strtotime(I('time2')) + 86400);
        }
        $this->assign($data);
        $this->_list('finance_log', $where);
    }


    //分享书币
    public function share()
    {
        $where = [];
        if (IS_POST) {
            $_GET['p'] = 1;
        }
        $data['user_id'] = I('user_id');
        $data['self_id'] = I('self_id');
        $data['time']    = I('time');
        if (!empty($data['user_id'])) {
            $where['user_id'] = intval($data['user_id']);
        }
        if (!empty($data['self_id'])) {
            $where['self_id'] = intval($data['self_id']);
        }
        if (!empty($data['time'])) {
            $where['date'] = $data['time'];
        }
        $this->assign($data);
        $this->_list("slog", $where, 'create_time desc');
    }


}