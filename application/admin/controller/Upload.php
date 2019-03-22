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
            $time=input('post.time');

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
            $sql = Db::table('think_user')->order('create_time desc')->select();
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


                //设置需要添加颜色的坐标,样式限定添加在这个时间之后
                if($sql[$i-2]['create_time']>$time)
                {
                    $m=$i;
                }
            }
            /*--------------下面是设置其他信息------------------*/



            //设置表格框线
            $styleThinBlackBorderOutline = array(
                'borders' => array(
                    'allborders' => array( //设置全部边框
                        'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                    ),

                ),
            );
            $j=$i-1;
            $zb='A1:'.'F'.$j;//设置边框的单元格坐标，从A1开始到F$j
            $objPHPExcel->getActiveSheet()->getStyle( $zb)->applyFromArray($styleThinBlackBorderOutline);


            //设置单元格背景颜色
            $colorzb='A2:'.'F'.$m;//添加背景颜色的坐标
            $objPHPExcel->getActiveSheet()->getStyle( $colorzb)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle( $colorzb)->getFill()->getStartColor()->setARGB('FFFFFF00');







            $objPHPExcel->getActiveSheet()->setTitle('info');      //设置sheet的名称
            $objPHPExcel->setActiveSheetIndex(0);                   //设置sheet的起始位置


                //第二个sheet
            $objPHPExcel->createSheet();
            $objPHPExcel->setActiveSheetIndex(1);
            $num = 1;
            $this->makeData($objPHPExcel,$num);
//            $this->createSheets($objPHPExcel);


            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来

            $PHPWriter = \PHPExcel_IOFactory::createWriter( $objPHPExcel,"Excel2007");
                       //设置文件名
            header('Content-Disposition: attachment;filename="数据库文件.xlsx"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $excel=$PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

        }

        protected function makeData($objPHPExcel,$num)
        {
            $objPHPExcel->createSheet();
            $objPHPExcel->setActiveSheetIndex($num);
            //$objPHPExcel->getActiveSheet()->setCellValue('A1', '第二个表');//在这个sheet表格之前的头部标题

            //设置居中
            $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            //设置单元格宽
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);

            $sql = Db::table('think_person')->order('id desc')->select();


            $objPHPExcel->setActiveSheetIndex(1)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', '名字')
                ->setCellValue('C1', '创建时间')
                ->setCellValue('D1', '更新时间');
            /*--------------开始从数据库提取信息插入Excel表中------------------*/

            $i = 2;  //定义一个i变量，目的是在循环输出数据是控制行数
            $count = count($sql);  //计算有多少条数据
            //数据库中需要导出的项
            for ($i = 2; $i <= $count + 1; $i++) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $sql[$i - 2]['id']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $sql[$i - 2]['name']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $sql[$i - 2]['create_time']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $sql[$i - 2]['update_time']);
            }

                //设置表格框线
                $styleThinBlackBorderOutline = array(
                    'borders' => array(
                        'allborders' => array( //设置全部边框
                            'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                        ),

                    ),
                );
                $j = $i - 1;
                $zb = 'A1:' . 'D' . $j;//设置边框的单元格坐标，从A1开始到F$j
                $objPHPExcel->getActiveSheet()->getStyle($zb)->applyFromArray($styleThinBlackBorderOutline);


                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
                /*保存到本地*/
                $objPHPExcel->getActiveSheet()->setTitle('person');//sheet名称
                return true;


        }


}