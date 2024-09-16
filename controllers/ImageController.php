<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\ImageUpload;

class ImageController extends Controller
{
    public function actionUpload()
    {
        $model = new ImageUpload();

        if (Yii::$app->request->isPost) {
            $model->image = UploadedFile::getInstance($model, 'image');

            if ($model->validate()) {
                $filePath = 'images/' . uniqid() . '.' . $model->image->extension;
                $fileStream = fopen($model->image->tempName, 'rb');

                // Save the image to AWS S3
                Yii::$app->filesystem->writeStream($filePath, $fileStream);
                fclose($fileStream);

                return $this->asJson(['success' => true, 'path' => $filePath]);
            } else {
                return $this->asJson(['success' => false, 'errors' => $model->errors]);
            }
        }

        return $this->render('upload', ['model' => $model]);
    }
}
