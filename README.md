# Login Controller Endpoint

## Overview

This repo contains the user source code for the login controller. The sole purpose of this endpoint is to act as a proxy server for handling user-login within the application. Filtering faulty requests and ensuring users are properly redirected to when 2FA is enabled.

## Endpoint URIs

* ### **`/login/basic`**

##### Description: Logs in users

> * http verb: *`POST`*
> * reponse code: `200`
> * response type: *JSON*
>
> Returns a User object (see [user endpoint][user-repo] for more information)

##### Url Body Paramaters:

|param|description|type|default|
|---:|---|:---:|:---:| 
| **`email`** | A valid email of the user account to login with |*string* |
| **`password`** | The password attributed to the account |*string* |

#### Usage

Login with credentials:
* `email`: **example@email.com**
* `password`: **alphanumerical_1234**

Will yeild:

```json
{
    "auth": {
        "enabled": false
    },
    "steamid": null,
    "_id": "759c48df-7815-41a6-a373-940470bb1b6f",
    "username": "COMPLETE-HERON",
    "email": "example@email.com",
    "token": "cfa115a13e52761d858e0ac0f36e00",
    "created_at": "Sun Jan 10 2021"
}
```

<br/>

* ### **`/login/2fa`**

##### Description: Logs in users with 2-factor-authentification enabled

> * http verb: *`POST`*
> * reponse code: `200`
> * response type: *JSON*
>
> Returns a User object (see [user endpoint][user-repo] for more information)

##### Url Body Paramaters:

|param|description|type|default|
|---:|---|:---:|:---:| 
| **`_id`** | The uuid of the user to be used when login in the user  |*string* |
| **`token`** | The temporary (~30 second) valid token generated and sent to the user vie their email |*integer* |

#### Usage

* ##### Step 1:

Login with credentials:
* `email`: **example@email.com**
* `password`: **alphanumerical_1234**

Will yeild:

```json
{
    "_id": "759c48df-7815-41a6-a373-940470bb1b6f"
}
```

Where the `_id` field will be used on the frontend to handle the login with 2FA and, concurrently, an email has been sent to the user where they will find their temporary login token.

* ##### Step 2:

Sent 2fa form to login:
* `_id`: **759c48df-7815-41a6-a373-940470bb1b6f**
* `token`: **480255**

Will yeild:

```json
{
    "auth": {
        "enabled": true
    },
    "steamid": null,
    "_id": "759c48df-7815-41a6-a373-940470bb1b6f",
    "username": "COMPLETE-HERON",
    "email": "example@email.com",
    "token": "cfa115a13e52761d858e0ac0f36e00",
    "created_at": "Sun Jan 10 2021"
}
```


[user-repo]: https://github.com/noahgreff/user-api-endpoint/

