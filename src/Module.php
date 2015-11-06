<?php

namespace DotPlant\Keywords;

use app\backend\components\BackendController;
use app\backend\controllers\DynamicContentController;
use app\backend\events\BackendEntityEditEvent;
use app\backend\events\BackendEntityEditFormEvent;
use app\components\ExtensionModule;
use app\models\DynamicContent;
use app\models\Object;
use app\modules\page\backend\PageController as BackendPageController;
use app\modules\page\controllers\PageController;
use app\modules\page\models\Page;
use app\modules\shop\controllers\BackendCategoryController;
use app\modules\shop\controllers\BackendProductController;
use app\modules\shop\controllers\ProductController;
use DotPlant\Keywords\models\DynamicContentKeywords;
use DotPlant\Keywords\models\ObjectKeyword;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\ViewEvent;
use yii\base\Application;
use yii\web\View;

class Module extends ExtensionModule implements BootstrapInterface
{
    public static $moduleId = 'Keywords';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'configurableModule' => [
                'class' => 'app\modules\config\behaviors\ConfigurableModuleBehavior',
                'configurationView' => '@keywords/views/configurable/_config',
                'configurableModel' => 'DotPlant\Keywords\components\ConfigurationModel',
            ]
        ];
    }

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->on(
            Application::EVENT_BEFORE_ACTION,
            function () use ($app) {
                if ($app->requestedAction->controller instanceof BackendController) {
                    $backendControllers = [
                        [
                            'class' => BackendPageController::className(),
                            'editSaveConst' => BackendPageController::BACKEND_PAGE_AFTER_SAVE,
                            'editFormConst' => BackendPageController::BACKEND_PAGE_EDIT_FORM,
                        ],
                        [
                            'class' => BackendProductController::className(),
                            'editSaveConst' => BackendProductController::EVENT_BACKEND_PRODUCT_AFTER_SAVE,
                            'editFormConst' => BackendProductController::EVENT_BACKEND_PRODUCT_EDIT_FORM,
                        ],
                        [
                            'class' => BackendCategoryController::className(),
                            'editSaveConst' => BackendCategoryController::BACKEND_CATEGORY_AFTER_SAVE,
                            'editFormConst' => BackendCategoryController::BACKEND_CATEGORY_EDIT_FORM,
                        ],
                        [
                            'class' => DynamicContentController::className(),
                            'editSaveConst' => DynamicContentController::BACKEND_DYNAMIC_CONTENT_EDIT_SAVE,
                            'editFormConst' => DynamicContentController::BACKEND_DYNAMIC_CONTENT_EDIT_FORM,
                        ],
                    ];
                    foreach ($backendControllers as $backendController) {
                        $className = $backendController['class'];
                        if ($app->requestedAction->controller instanceof $className) {
                            BackendEntityEditEvent::on(
                                $className,
                                $backendController['editSaveConst'],
                                [$this, 'saveHandler']
                            );
                            BackendEntityEditFormEvent::on(
                                View::className(),
                                $backendController['editFormConst'],
                                [$this, 'renderEditForm']
                            );
                            break;
                        }
                    }
                } elseif ($app->requestedAction->id == 'show' || $app->requestedAction->id == 'list') {
                    if ($app->requestedAction->controller instanceof ProductController) {
                        ViewEvent::on(
                            ProductController::className(),
                            ProductController::EVENT_PRE_DECORATOR,
                            [$this, 'registerMeta']
                        );
                    } elseif ($app->requestedAction->controller instanceof PageController) {
                        ViewEvent::on(
                            PageController::className(),
                            PageController::EVENT_PRE_DECORATOR,
                            [$this, 'registerMeta']
                        );
                    }
                }
            }
        );
    }

    /**
     * Render keyword edit form in backend
     * @param BackendEntityEditFormEvent $event
     * @return null
     */
    public function renderEditForm(BackendEntityEditFormEvent $event)
    {
        if (isset($event->model) === false) {
            return null;
        }
        /** @var \yii\web\View $view */
        $view = $event->sender;
        $model = $event->model;
        $keywordObject = static::loadModel($model);
        echo $view->render(
            '@keywords/views/backend/_edit',
            [
                'form' => $event->form,
                'model' => $event->model,
                'keywordObject' => $keywordObject,
            ]
        );
    }

    /**
     * @param $model
     * @param bool|true $createNew
     * @return array|ObjectKeyword|DynamicContentKeywords|null|\yii\db\ActiveRecord
     */
    public static function loadModel($model, $createNew = true)
    {
        if ($model instanceof DynamicContent) {
            $keyword = DynamicContentKeywords::findOne(['dynamic_content_id' => $model->id]);
            if ($createNew === true) {
                if (is_null($keyword) === true) {
                    $keyword = new DynamicContentKeywords;
                    $keyword->dynamic_content_id = $model->id;
                }
                $keyword->load(Yii::$app->request->post());
            }
        } else {
            $object = Object::getForClass($model::className());
            if (is_null($object)) {
                return null;
            }
            $keyword = ObjectKeyword::find()->where(
                [
                    'object_id' => $object->id,
                    'object_model_id' => $model->id
                ]
            )->one();
            if ($createNew === true) {
                if (is_null($keyword) === true) {
                    $keyword = new ObjectKeyword();
                    $keyword->object_id = $model->object->id;
                    $keyword->object_model_id = $model->id;
                }
                $keyword->load(Yii::$app->request->post());
            }
        }

        return $keyword;
    }

    /**
     * @param $event
     * @return null
     * @throws \Exception
     */
    public function saveHandler($event)
    {
        if (isset($event->model) === false) {
            return null;
        }

        $model = $event->model;
        $keyword = static::loadModel($model);
        if (empty($keyword->keywords) === true) {
            $keyword->delete();
        } elseif ($keyword->save() === true) {
            Yii::$app->session->setFlash('info', 'Keyword Save');
        }
    }

    /**
     * @param ViewEvent $event
     * @return null
     */
    public function registerMeta(ViewEvent $event)
    {
        if (empty($event->params['model']) === true) {
            return null;
        }
        if (Yii::$app->response->dynamic_content_trait === true) {
            $model = Yii::$app->response->matched_dynamic_content_trait_model;
        } else {
            $model = $event->params['model'];
        }
        $keyword = static::loadModel($model, false);
        if (is_null($keyword) === false) {
            Yii::$app->view->registerMetaTag(['name' => 'keywords', 'content' => $keyword->keywords]);
        }
    }

}
