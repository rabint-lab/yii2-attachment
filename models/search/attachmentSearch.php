<?php

namespace rabint\attachment\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use rabint\attachment\models\Attachment;

/**
 * attachmentSearch represents the model behind the search form about `rabint\attachment\models\Attachment`.
 */
class attachmentSearch extends Attachment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'size', 'created_at', 'updated_at', 'weight', 'protected'], 'integer'],
            [['component', 'path', 'title', 'name', 'extension', 'type', 'mime', 'ip', 'meta'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param boolean $returnActiveQuery
     *
     * @return ActiveDataProvider OR ActiveQuery
     */
    public function search($params,$returnActiveQuery = FALSE)
    {
        $query = Attachment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'size' => $this->size,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'weight' => $this->weight,
            'protected' => $this->protected,
        ]);

        $query->andFilterWhere(['like', 'component', $this->component])
            ->andFilterWhere(['like', 'path', $this->path])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'extension', $this->extension])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'mime', $this->mime])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'meta', $this->meta]);

        if ($returnActiveQuery) {
            return $query;
        }
        return $dataProvider;
    }
}
