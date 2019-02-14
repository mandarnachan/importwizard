<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class Category extends Controller
{		
		
		public function actionImport(){
			$modelImport = new \yii\base\DynamicModel([
						'fileImport'=>'File Import',
					]);
			$modelImport->addRule(['fileImport'],'required');
			$modelImport->addRule(['fileImport'],'file',['extensions'=>'ods,xls,xlsx'],['maxSize'=>1024*1024]);

			if(Yii::$app->request->post()){
				$modelImport->fileImport = \yii\web\UploadedFile::getInstance($modelImport,'fileImport');
				if($modelImport->fileImport && $modelImport->validate()){
					$inputFileType = \PHPExcel_IOFactory::identify($modelImport->fileImport->tempName);
					$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($modelImport->fileImport->tempName);
					$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					$baseRow = 3;
					while(!empty($sheetData[$baseRow]['B'])){
						$model = new \common\models\Category;
						$model->title = (string)$sheetData[$baseRow]['B'];
						$model->description = (string)$sheetData[$baseRow]['C'];
						$model->save();
						$baseRow++;
					}
					Yii::$app->getSession()->setFlash('success','Success');
				}else{
					Yii::$app->getSession()->setFlash('error','Error');
				}
			}

			return $this->render('import',[
					'modelImport' => $modelImport,
				]);
		}
}
?>		