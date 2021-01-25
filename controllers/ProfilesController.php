<?php

namespace wdmg\profiles\controllers;

use Yii;
use wdmg\profiles\models\Profiles;
use wdmg\profiles\models\ProfilesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ProfilesController implements the CRUD actions for Profiles model.
 */
class ProfilesController extends Controller
{
    /**
     * @var string|null Selected language (locale)
     */
    private $_locale;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $this->_locale = Yii::$app->request->get('locale', null);
        return parent::beforeAction($action);
    }

    /**
     * Lists all profiles.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProfilesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Profiles();
        $model->setScenario($model::SCENARIO_CREATE);

        // No language is set for this model, we will use the current user language
        if (is_null($model->locale)) {
            if (is_null($this->_locale)) {

                $model->locale = Yii::$app->sourceLanguage;
                if (!Yii::$app->request->isPost) {

                    $languages = $model->getLanguagesList(false);
                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/profiles',
                            'No display language has been set. Source language will be selected: {language}',
                            [
                                'language' => (isset($languages[Yii::$app->sourceLanguage])) ? $languages[Yii::$app->sourceLanguage] : Yii::$app->sourceLanguage
                            ]
                        )
                    );
                }
            } else {
                $model->locale = $this->_locale;
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save())
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app/modules/profiles', 'User profile has been successfully created!')
                );
            else
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app/modules/profiles', 'An error occurred while creating the user profile.')
                );

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // No language is set for this model, we will use the current user language
        if (is_null($model->locale)) {

            $model->locale = Yii::$app->sourceLanguage;
            if (!Yii::$app->request->isPost) {

                $languages = $model->getLanguagesList(false);
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/profiles',
                        'No display language has been set. Source language will be selected: {language}',
                        [
                            'language' => (isset($languages[Yii::$app->sourceLanguage])) ? $languages[Yii::$app->sourceLanguage] : Yii::$app->sourceLanguage
                        ]
                    )
                );
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save())
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app/modules/profiles', 'User profile has been successfully updated!')
                );
            else
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app/modules/profiles', 'An error occurred while updating the user profile.')
                );

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete())
            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t('app/modules/profiles', 'User profile has been successfully deleted!')
            );
        else
            Yii::$app->getSession()->setFlash(
                'danger',
                Yii::t('app/modules/profiles', 'An error occurred while deleting the user profile.')
            );

        return $this->redirect(['index']);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Profiles::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/modules/profiles', 'The requested profile does not exist.'));
    }

    /**
     * Return current locale for dashboard
     *
     * @return string|null
     */
    public function getLocale() {
        return $this->_locale;
    }
}
