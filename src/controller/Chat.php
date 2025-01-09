<?php

namespace Minga\Demo\controller;

use think\facade\Db;

class Chat
{

    /**
     * 撤回
     */
    public function delChat()
    {
        $data = $this->request->post();
        $this->_vali([
            'id.require' => 'id不能为空！',
        ],$data);
        $res = Db::name("communication")->where('id',$data['id'])->delete();
        if ($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    /**
     *文本消息的数据持久化
     */
    public function save_message(){
            $message = input("post.");
            if (empty($message)){
                $this->error('信息不存在!');
            }
            $datas['fromid']=$message['fromid'];
            $datas['fromname']= $this->getName($datas['fromid']);
            $datas['toid']=$message['toid'];
            $datas['toname']= $this->getName($datas['toid']);
            $datas['data']=$message['data'];
            $datas['time']=$message['time'];
//            $datas['isread']=$message['isread'];
            $datas['isread']=0;
            $datas['type'] = 1;
           $res =  Db::name("communication")->insert($datas);
           if ($res){
               $this->success('发送成功');
           }else{
               $this->error('发送失败');
           }
    }

    /**
     * 根据用户id返回用户姓名
     */
    public function getName($uid){

        $userinfo = Db::name("data_user")->where('id',$uid)->field('nickname')->find();

        return $userinfo['nickname'];
    }
    /**
     * 根据用户id获取聊天双方的头像信息；
     */
    public function get_head(){

            $fromid = input('fromid');
            $toid = input('toid');

            $frominfo = Db::name('data_user')->where('id',$fromid)->find();
            $toinfo = Db::name('data_user')->where('id',$toid)->find();
            $data =  [
                'from_head'=>$frominfo['headimg'],
                'from_name'=>$frominfo['nickname'],
                'to_head'=>$toinfo['headimg'],
                'to_name'=>$toinfo['nickname']
            ];
           $this->success('success',$data);
        }
    /**
     * 根据用户id返回用户姓名；
     */
    public function get_name(){
            $uid = input('uid');
            $toinfo = Db::name('data_user')->where('id',$uid)->field('nickname')->find();
            return ["toname"=>$toinfo['nickname']];
    }
    /**
     * 页面加载返回聊天记录
     */

    public function load()
    {
        $fromid = input('fromid');
        $toid = input('toid');
        $page = isset($input['page']) ? intval($input['page']) : 1;
        // 获取每页显示的条数，默认为10
        $limit = isset($input['limit']) ? intval($input['limit']) : 10;
        $message = Db::table('communication')
            ->where(function ($query) use ($fromid, $toid) {
                $query->where('fromid', $fromid)->where('toid', $toid);
            })
            ->whereOr(function ($query) use ($fromid, $toid) {
                $query->where('fromid', $toid)->where('toid', $fromid);
            })
            ->order('id desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page,
            ])->toArray();
    foreach ($message['data'] as &$vs){
        $vs['time'] = date('Y-m-d H:i:s',$vs['time']);
    }
     $this->success('success',$message);

    }
    /**
     * @param $uid
     * 根据uid来获取它的头像
     */
    public function get_head_one($uid){

        $fromhead = Db::name('data_user')->where('id',$uid)->field('headimg')->find();

        return $fromhead['headimg'];
    }
    /**
     * @param $fromid
     * @param $toid
     * 根据fromid来获取fromid同toid发送的未读消息。
     */
    public function getCountNoread($fromid,$toid){

        return Db::name('communication')->where(['fromid'=>$fromid,'toid'=>$toid,'isread'=>0])->count('id');

    }
    public function getLastMessage($fromid,$toid){

        $info = Db::name('communication')->where('(fromid=:fromid&&toid=:toid)||(fromid=:fromid2&&toid=:toid2)',['fromid'=>$fromid,'toid'=>$toid,'fromid2'=>$toid,'toid2'=>$fromid])->order('id DESC')->limit(1)->find();

        return $info;
    }

    public function get_list(){

        $fromid = input('id');
        $list = Db::table('communication')->where('fromid', $fromid)->group('toid')->field('toid,time')->order('time desc')->select()->toArray();
        $toid_array = [];
        foreach ($list as $item){
            $toid_array[] = $item['toid'];
        }
        $list2 = Db::table('communication')->where('toid', $fromid)->whereNotIn('fromid',$toid_array)->group('fromid')->field('fromid as toid,time')->order('time desc')->select()->toArray();
        // 将两个数组合并到一个新的数组中
        $newList = array_merge($list, $list2);

        $cmf_arr = array_column($newList, 'time');
        array_multisort($cmf_arr, SORT_DESC, $newList);

        for($i=0;$i<count($newList);$i++){
            $to_user = Db::table('data_user')->where('id', $newList[$i]['toid'])->find();

            if(!empty($to_user)){
                $newList[$i]['nickname'] = $to_user['nickname'];
                $newList[$i]['headimg'] = $to_user['headimg'];
                $newList[$i]['base_sex'] = $to_user['base_sex'];
            }

//            $count1 =  Db::table('communication')->where('fromid', $fromid)->where('toid', $newList[$i]['toid'])->where('isread',0)->count();
            $count2 = Db::table('communication')->where('fromid', $newList[$i]['toid'])->where('toid',$fromid)->where('isread',0)->count();
//            $newList[$i]['to_wd_count'] =  $count1+$count2;
            $newList[$i]['to_wd_count'] = $count2;
            $newList[$i]['msg'] = Db::table('communication')->whereRaw('(fromid = '.$fromid.' and toid = '.$newList[$i]['toid'].') or (fromid = '.$newList[$i]['toid'].' and toid = '.$fromid.')')->field('fromid,toid,data,time,type')->order('time desc')->find();
            $newList[$i]['msg']['time'] = date('Y-m-d H:i:s',$newList[$i]['msg']['time']);
        }
        $this->success('success',$newList);
    }
    public function changeNoRead(){
        $toid = input('toid');
        $fromid = input('fromid');
           $res =  Db::name('communication')->where(['fromid'=>$fromid,"toid"=>$toid])->where('isread',0)->select()->toArray();
           foreach ($res as &$vs){
               Db::name('communication')->where(['id'=>$vs['id']])->update(['isread'=>1,'up_time'=>time()]);
           }
            if ($res){
                $this->success('success');
            }else{
                $this->success('success');
            }
    }
}