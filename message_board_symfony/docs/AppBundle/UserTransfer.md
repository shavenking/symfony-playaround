# 前言

為了專注在程式設計，寫了一個 Command 方便建立使用者，並使用 Http Basic Authentication 做使用者驗證。

API 設計參考 [jsonapi.org](jsonapi.org)

# APIs

## 會員明細 API

- `GET /transfers.json` to request a JSON response
- `GET /transfers.html` to request a HTML response

**URL Parameter**

- `page`
- `limit`

Example: `/transfers?page=1&limit=20`

**Response**: `JSON`, `HTML`

```json
{
    "data": [{
        "id": 1,
        "amount": 123,
        "transferedAt": {} // DateTime
    }, /*...*/]
    "meta": {
        "firstPage": 1,
        "lastPage": 13,
        "limit": 10,
        "count": 121 // total transfers count
    }
}
```

```
// AppBundle:Transfer:index.html.twig
```

## 出入款 API

- 出款 `POST /withdrawals` 或 `POST /withdrawals.json`
- 入款 `POST /deposits` 或 `POST /deposits.json`

**Request**: `JSON` only

- `amount` 必須為大於 0 的正數

**Response**: `JSON` only

```json
{
    "data": {
        "id": 1// integer
        "amount": 123// integer
        "transferedAt": {} // DateTime
    }
}
```

### 實作細節

1. 開始 Transaction
2. 取出最新一筆 `Transfer` 後的餘額，並鎖定讀取（`\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE`）
3. 計算餘額並將新的 `Transfer` 寫入資料庫
4. 結束 Transaction

# 資料庫結構

## 使用者

```
mysql> desc users;
+----------+-------------+------+-----+---------+----------------+
| Field    | Type        | Null | Key | Default | Extra          |
+----------+-------------+------+-----+---------+----------------+
| id       | int(11)     | NO   | PRI | NULL    | auto_increment |
| username | varchar(50) | NO   | UNI | NULL    |                |
| password | varchar(60) | NO   |     | NULL    |                |
+----------+-------------+------+-----+---------+----------------+
```

## 交易紀錄

```
mysql> desc transfers;
+---------------+----------+------+-----+---------+----------------+
| Field         | Type     | Null | Key | Default | Extra          |
+---------------+----------+------+-----+---------+----------------+
| id            | int(11)  | NO   | PRI | NULL    | auto_increment |
| user_id       | int(11)  | YES  | MUL | NULL    |                |
| amount        | int(11)  | NO   |     | NULL    |                |
| balance       | int(11)  | NO   |     | NULL    |                |
| transfered_at | datetime | NO   |     | NULL    |                |
+---------------+----------+------+-----+---------+----------------+
```

# TODO

- [ ] Version APIs
- [ ] Replace `page` with `offset`
- [ ] Fix `Pagination` meta response (respond `count`, `offset`, `limit` only)
