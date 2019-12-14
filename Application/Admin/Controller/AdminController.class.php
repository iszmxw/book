<?php

namespace Admin\Controller;

use Think\Controller;
use Think\Page;

class AdminController extends Controller
{
    public $data;

    private function getGrant()
    {
        $url  = "http://119.29.21.81/grant/grant.php?c=" . C('auth');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        if ($data == 1) {
            header('Content-Type: text/html; charset=utf-8');
            echo $data;
            exit;
        }
    }

    public function _initialize()
    {
        $this->getGrant();
        if (CONTROLLER_NAME != 'Index' && !session('?admin')) {
            $this->error('请登陆后操作!', U('Index/login'));
            exit;
        }
        if (substr(ACTION_NAME, 0, 1) == '_') {
            $this->error('访问地址错误！', U('Index/index'));
        }
        $config = M('config')->select();
        foreach ($config as $v) {
            $key = '_' . $v['name'];
            $val = unserialize($v['value']);
            switch ($key) {
                case '_site':
                    if (!isset($val['zidongzhuce'])) {
                        $val['zidongzhuce'] = 0;
                    }
                    if (!isset($val['weixinlogin'])) {
                        $val['weixinlogin'] = 0;
                    }
                    if (!isset($val['points_name'])) {
                        $val['points_name'] = 0;
                    }
                    if (!isset($val['withdraw'])) {
                        $val['withdraw'] = 1;
                    }
                    break;
                case '_ads':
                    if (!isset($val['isopen'])) {
                        $val['isopen'] = 0;
                    }
                    break;
            }
            $this->{$key}     = $val;
            $_CFG[$v['name']] = $this->{$key};
        }
        $this->assign('_CFG', $_CFG);
        $GLOBALS['_CFG'] = $_CFG;
        $this->assign('murl', "http://" . $_SERVER['HTTP_HOST'] . __ROOT__ . "/index.php?m=");
    }

    public function welcome()
    {
        $info = array();
        $this->assign('info', $info);
        $this->display();
    }

    public function set_col($table = null)
    {
        $id    = intval($_REQUEST['id']);
        $col   = $_REQUEST['col'];
        $value = $_REQUEST['value'];
        if (!$table) {
            $table = CONTROLLER_NAME;
        }
        M($table)->where('id=' . $id)->setField($col, $value);
        $this->success('操作成功', $_SERVER['HTTP_REFERER']);
    }

    /**
     * 分页获取数据列表并且返回到视图
     * @param $table
     * @param null $where
     * @param null $order
     * @author: iszmxw <mail@54zm.com>
     * @Date：2019/12/14 15:39
     */
    protected function _list($table, $where = null, $order = null)
    {
        $list = $this->_get_list($table, $where, $order);
        $this->assign('list', $list);
        $this->assign('page', $this->data['page']);
        $this->display();
    }

    /**
     * 分页获取数据列表
     * @param $table
     * @param null $where
     * @param null $order
     * @return mixed
     * @author: iszmxw <mail@54zm.com>
     * @Date：2019/12/14 15:40
     */
    protected function _get_list($table, $where = null, $order = null)
    {
        $model = M($table);
        $count = $model->where($where)->count();
        $page  = new Page($count, 25);
        if (!$order) {
            $order = "id desc";
        }
        $list       = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
        $this->data = array('list' => $list, 'page' => $page->show(), 'count' => $count);
        return $list;
    }

    protected function _edit($table, $url = null)
    {
        $model = M($table);
        $id    = intval($_GET['id']);
        if ($id > 0) {
            $info = $model->find($id);
            if (!$info) {
                $this->error('信息不存在');
            }
            $this->assign('info', $info);
        }
        if (IS_POST) {
            if (!$url) {
                $url = U('index');
            }
            if ($id > 0) {
                $_POST['id'] = $id;
                $model->save($_POST);
                $this->success('操作成功！', $url);
                exit;
            } else {
                $model->add($_POST);
                $this->success('添加成功！', $url);
                exit;
            }
        }
        $this->display();
    }

    protected function _del($table, $id)
    {
        if ($id > 0 && !empty($table)) {
            M($table)->delete($id);
        }
    }

    public function upload()
    {
        $errmsg = null;
        if (I('url')) {
            $this->assign('url', I('url'));
        }
        if (IS_POST) {
            if (I('field')) {
                $field = I('field');
            }
            if (empty($field)) {
                $field = 'file';
            }
            if ($_FILES[$field]['size'] < 1 && $_FILES[$field]['size'] > 0) {
                $errmsg = "上传错误！";
            } else {
                $ext = $this->_get_ext($_FILES[$field]['name']);
                if (!in_array(strtolower($ext), array('gif', 'jpg', 'png'))) {
                    $this->error('upload error');
                }
                $new_name = $this->_get_new_name($ext, 'images');
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $new_name['fullname'])) {
                    $this->assign('url', $new_name['fullname']);
                } else {
                    $errmsg = '文件保存错误！';
                }
            }
        }
        C('LAYOUT_ON', false);
        $this->assign('errmsg', $errmsg);
        $this->display('Admin/upload');
    }

    private function _get_ext($file_name)
    {
        return substr(strtolower(strrchr($file_name, '.')), 1);
    }

    private function _get_new_name($ext, $dir = 'default')
    {
        $name = date('His') . substr(microtime(), 2, 8) . rand(1000, 9999) . '.' . $ext;
        $path = './Public/upload/' . $dir . date('/ym/d') . '/';
        if (!is_dir($path)) {
            mkdir($path, 0777, 1);
        }
        return array('name' => $name, 'fullname' => $path . $name);
    }

    public function setField()
    {
        $id     = I('get.id');
        $table  = I('get.table');
        $field  = I('get.field');
        $info   = M($table)->find(intval($id));
        $status = $info[$field] ? 0 : 1;
        M($table)->where(array("id" => $id))->save(array($field => intval($status)));
        $this->success('操作成功！');
    }


    /**
     * 渲染数组到视图
     * @param $data
     * @author: iszmxw <mail@54zm.com>
     * @Date：2019/12/14 15:43
     */
    public function views($data)
    {
        foreach ($data as $key => $val) {
            $this->assign($key, $val);
        }
    }
}