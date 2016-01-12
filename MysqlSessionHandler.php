<?php
class MysqlSessionHandler
{
    /**
     * MySQLサーバーへの接続先
     * @var string
     */
    private $_host = 'localhost';
 
    /**
     * MySQLサーバーの接続ユーザー
     * @var string
     */
    private $_user = 'phptest';
    /**
     * MySQLサーバーの接続パスワード
     * @var string
     */
    private $_pass = 'phptest';
    /**
     * MySQLサーバーの接続データベース
     * @var string
     */
    private $_db = 'phptest';
 
    /**
     * MySQL接続情報を持つ
     * @var mysqli
     */
    private $_link = null;
 
    /**
     * セッションを開始する際に呼び出される。MySQLへの接続を開始する。
     * @param  string $savePath session.save_pathで設定されているパス
     * @param  string $saveName session.nameで設定されている名前(PHPSESSID)
     * @return bool
     */
    function open($savePath, $saveName)
    {
        $this->_link = mysqli_init();
        return mysqli_real_connect($this->_link, $this->_host, $this->_user, $this->_pass, $this->_db);
    }
 
    /**
     * セッションを閉じる際に呼び出される。MySLQへの接続を閉じる。
     * @return bool
     */
    function close()
    {
        if(!is_null($this->_link))
        {
            return mysqli_close($this->_link);
        }
        return true;
    }
 
    /**
     * セッションのデータを読み込む。対象のレコードを取り出してデータを返す
     * @param  string $id セッションID
     * @return string セッションのデータ
     */
    function read($id)
    {
        $id = mysqli_real_escape_string($this->_link, $id);
        $sql = "SELECT * FROM session WHERE id LIKE '${id}'" ;
        $result = mysqli_query($this->_link, $sql);
        $row = mysqli_fetch_assoc($result);
        if(!empty($row))
        {
            return $row['data'];
        }
        return null;
    }
    /**
     * セッションのデータを書き込む。レコードを追加・更新する
     * @param  string $id セッションID
     * @param  string $data セッションのデータ $_SESSIONをシリアライズしたもの
     * @return bool
     */
    function write($id, $data)
    {
        $id = mysqli_real_escape_string($this->_link, $id);
        $data = mysqli_real_escape_string($this->_link, $data);
        $date = date("Y-m-d H:i:s");
        $sql = "REPLACE INTO session VALUES ('${id}', '${data}', '${date}')" ;
        mysqli_query($this->_link, $sql);
        return true;
    }
 
    /**
     * セッションを破棄する。対象のレコードを削除します。
     * @param  string $id セッションID
     * @return bool
     */
    function destroy($id)
    {
        $id = mysqli_real_escape_string($this->_link, $id);
        $sql = "DELETE FROM session WHERE id LIKE '${id}'" ;
        mysqli_query($this->_link, $sql);
        return true;
    }
 
    /**
     * 古いセッションを削除する。古いレコードを削除します。
     * @param  string $maxlifetime セッションのライフタイム session.gc_maxlifetimeの値
     * @return bool
     */
    function gc($maxlifetime)
    {
        $maxlifetime = preg_replace('/[^0-9]/', '', $maxlifetime);
        $sql = "DELETE FROM session WHERE (TIMESTAMP(CURRENT_TIMESTAMP) - TIMESTAMP(created)) > ${maxlifetime}" ;
        mysqli_query($this->_link, $sql);
        return true;
    }
}
