Привет, в общем... и целом.

Пример данных для импорта 
```json
{
"toyota": [
  {
    "name": "vitz",
    "attributes": {
      "color": "Зеленый",
      "steering_position": "ПО середине"
    }
  },
  {
    "name": "corola",
    "attributes": {
      "color": "Зеленый",
      "steering_position": "ПО середине"
    }
  },
  {
    "name": "ipsum",
    "attributes": {
      "color": "Зеленый",
      "steering_position": "ПО середине"
    }
  }
],
"nissan": [
  {
    "name": "там",
    "attributes": {
      "color": "Зеленый",
      "steering_position": "ПО середине"
    }
  },
  {
    "name": "тут",
    "attributes": {
      "color": "Зеленый",
      "steering_position": "ПО середине"
    }
  },
  {
    "name": "здесь",
    "attributes": {
      "color": "Зеленый",
      "steering_position": "ПО середине"
    }
  }
]
}
```
# Api URL
+ api/addData - [POST] Импорт данных

##Марки
+ api/brands - [GET] Получить все марки
+ api/brands?page=1 - [GET] Получить все марки с пагинацией (лимит 50 штук)
+ api/brands - [POST] Добавить новую марку
```json
{
  "name":"porsche"
}
```
+ api/brands/{id} - [DELETE] Удалить марку по id
+ api/brands/{id} - [PUT] Отредактировать марку по id
```json
{
  "name":"porsche123"
}
```

##Модели
+ api/models - [GET] Получить все модели
+ api/models?page=1 - [GET] Получить все модели с пагинацией (лимит 50 штук)
+ api/models?page=1&brand={brandid} - [GET] Получить все модели которые относятся к марке с brandId
+ api/models - [POST] Добавить новую модель
```json
{
  "name":"test",
  "attributes": {
    "steering_position":"Руль по середине =]",
    "color":"Прозрачная"
  },
  "brandId": 5015
}
```
+ api/models/{id} - [DELETE] Удалить модель по id
+ api/models/{id} - [PUT] Отредактировать модель по id
```json
{
  "name":"test",
  "attributes": {
    "steering_position":"Руль по середине =]",
    "color":"Прозрачная"
  },
  "brandId": 5015
}
```

+ api/login - [POST] Авторизация
```json
{
  "username":"admin",
  "password":"112233"
}
```
+ api/userMe - [GET] Тут я получаю роли и имя для фронта

На самом деле хотелось бы сделать все совем по другому, но пол недели я проболел, пол недели тупил с авторизацией... По итогу, что б успеть сделал, как сделал...

Есть какая-то обработка ошибок, но по хорошему бы все привести к одному ответу... Моя вина раскаиваюсь...

Кстати? есть автозаполнение данных, правда я сделал на разброс (к нисанну может оносится витс, а к таете импрезе и т.д. в общем там полный рандом это чисто для тестов)

`php bin/console doctrine:fixtures:load`
