<?php

/**
 * @author wanbin
 *
 */
class StatusCode{
	// 正常状态码
	const OK = 0;
	// 未指定的错误号
	const UNKNOWN_ERROR = 1;
	// 配置错误
	const CONFIG_ERROR = 2;
	// status constants definition
	const DATABASE_ERROR = 1000;
	// 用户不存在
	const USER_NOT_EXISTS = 1001;
	// 指定的item不存在
	const ITEM_NOT_EXISTS = 1002;
	// 动作不存在
	const ACTION_NOT_EXISTS = 1003;
}

