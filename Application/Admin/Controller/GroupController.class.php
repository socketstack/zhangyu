<?php
namespace Admin\Controller;
class GroupController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->adminDb = M('Admin');
        $this->groupDb = M("Auth_group");
        $this->groupAccessDb = M("Auth_group_access");
        $this->ruleDb = M('Auth_rule');
        $this->menuDb = M('Admin_menu');
    }
    /**
    * 管理组列表
    */
    public function index(){
        $count = $this->groupDb->count();
        $Page = new \Think\Page($count,20);
        $list = $this->groupDb->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('id desc')->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("group_list");
    }
    /**
    * 管理组增加
    */
    public function groupAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            if(!$data['title']){
                $this->error("请输入管理组名称");
            }else{
                $data['type'] = 1;
                $result = $this->groupDb->data($data)->add();
                if($result){
                    $this->success("操作成功");
                }else{
                    $this->error("操作失败");
                }
            }
        }else{
            layout(false);
            $this->display("group_add");
        }
    }

    /**
    * 管理组编辑
    */
    public function groupEdit(){
        if(IS_POST){
            $id = $where['id'] = I('id');
            $data = $_POST['info'];
            $result = $this->groupDb->where($where)->save($data);
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $id = $where['id'] = I('id');
            layout(false);
            $groupInfo = $this->groupDb->where($where)->find();
            $this->assign($groupInfo);
            $this->display("group_edit");
        }

    }
    /**
    *管理组删除
    */
    public function groupDelete(){
        if(IS_POST){
            $id = intval($_POST['id']);
            if($id == '1') $this->error("当前管理组不允许删除");
            $whereData['id'] = $id;
            // 判断当前管理组是否有会员
            $whereGroupAccess['group_id'] = $id;
            $groupAccessData = $this->groupAccessDb->where($whereGroupAccess)->find();
            if($groupAccessData){
                $this->error('当前管理组存在管理员');
            }
            $this->groupDb->data($whereData)->delete();
            $this->success('删除成功');
        }
    }
    // 管理组设置规则
    public function groupSettingRule(){
        if(IS_POST){
            $whereGroup['id'] = $id = I("groupId");
            $ids = I('ids');
            if($ids){
                $this->groupDb->where($whereGroup)->setField("rules",$ids);
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $whereGroup['id'] = $id = I("groupId");
            $groupData = $this->groupDb->where($whereGroup)->find();
            $groupData['rules']  =  explode(',',$groupData['rules']);
            $this->assign($groupData);
            // 获取
            $tree = new \Org\Tree\Tree;
            $data = $this->ruleDb->order('sort desc,id desc')->select();
            $ruleList = $tree->makeTree($data);
            $this->assign("ruleList",$ruleList);
            // layout(false);
            $this->display("group_rule_list");
        }
    }
    // 管理组设置菜单
    public function groupSettingMenu(){
        if(IS_POST){
            $whereGroup['id'] = $id = I("groupId");
            $ids = I('ids');
            if($ids){
                $this->groupDb->where($whereGroup)->setField("menu_ids",$ids);
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $whereGroup['id'] = $id = I("groupId");
            $groupData = $this->groupDb->where($whereGroup)->find();
            $groupData['menu_ids']  =  explode(',',$groupData['menu_ids']);
            // dump($groupData);
            $this->assign($groupData);
            // 获取
            $tree = new \Org\Tree\Tree;
            $data = $this->menuDb->order('sort desc,id desc')->select();
            $menuList = $tree->makeTree($data);
            $this->assign("menuList",$menuList);
            $this->display("group_menu_list");
        }
    }

}