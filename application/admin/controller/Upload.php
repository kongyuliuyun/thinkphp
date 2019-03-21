<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use PHPExcel_IOFactory;
use PHPExcel;




class Upload extends Controller{

//        public function upload(){
//            vendor("PHPExcel.PHPExcel"); //方法一
//            $objPHPExcel = new \PHPExcel();
////获取表单上传文件
//            $file = request()->file('excel');
//            $info = $file->validate(['size'=>156780,'ext'=>'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'excel');
//            if($info){
////获取文件名
//                $exclePath = $info->getSaveName();
////上传文件的地址
//                $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;
//                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
////加载文件内容,编码utf-8
//                $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');
//
//                echo "<pre>";
//                $excel_array = $obj_PHPExcel->getsheet(0)->toArray(); //转换为数组格式
//                array_shift($excel_array); //删除第一个数组(标题);
//                $data = [];
//                foreach ($excel_array as $k => $v) {
//                    $data[$k]['name'] = $v['0'];
//                    $data[$k]['gender'] = $v['1'];
//                    $data[$k]['address'] = $v['2'];
//                }
////批量插入数据
//                $success = Db::name('info')->insertAll($data);
//                echo '数据添加成功';
//            }else{
//// 上传失败获取错误信息
//                echo $file->getError();
//            }
//
//        }
        public function download(){
            import('vendor.PHPExcel.PHPExcel');
            $path = dirname(__FILE__); //找到当前脚本所在路径
            vendor("PHPExcel.PHPExcel.PHPExcel");
            vendor("PHPExcel.PHPExcel.Writer.IWriter");
            vendor("PHPExcel.PHPExcel.Writer.Abstract");
            vendor("PHPExcel.PHPExcel.Writer.Excel5");
            vendor("PHPExcel.PHPExcel.Writer.Excel2007");
            vendor("PHPExcel.PHPExcel.IOFactory");
            $objPHPExcel = new \PHPExcel();
            $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
            // 实例化完了之后就先把数据库里面的数据查出来
            $sql = Db::table('think_user')->select();
            // 设置表头信息
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', '名字')
                ->setCellValue('C1', '创建时间')
                ->setCellValue('D1', '更新时间')
                ->setCellValue('E1', 'ip地址')
                ->setCellValue('F1', '邮箱');
            /*--------------开始从数据库提取信息插入Excel表中------------------*/

            $i=2;  //定义一个i变量，目的是在循环输出数据是控制行数
            $count = count($sql);  //计算有多少条数据
            //数据库中需要导出的项
            for ($i = 2; $i <= $count+1; $i++) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $sql[$i-2]['id']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $sql[$i-2]['name']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $sql[$i-2]['create_time']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $sql[$i-2]['update_time']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $sql[$i-2]['ip']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $sql[$i-2]['email']);

            }

            /*--------------下面是设置其他信息------------------*/

            $objPHPExcel->getActiveSheet()->setTitle('companyInformation');      //设置sheet的名称
            $objPHPExcel->setActiveSheetIndex(0);                   //设置sheet的起始位置
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来

            $PHPWriter = \PHPExcel_IOFactory::createWriter( $objPHPExcel,"Excel2007");
                       //设置文件名
            header('Content-Disposition: attachment;filename="数据库文件.xlsx"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $excel=$PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
            if($excel){
                $this->success('下载数据成功');
            }
        }

}