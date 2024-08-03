<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>API信息</title>
  </head>
  <body>
    <div class="layui-container">
      <table class="layui-hide" id="ID-friend-data" lay-filter="frienddata"></table>
      <button
        id="add-friend"
        type="button"
        class="layui-btn layui-btn-fluid layui-btn-lg layui-btn-normal"
      >
        添加API信息
      </button>
    </div>

    <script>
      function setpage() {
        //该方法是为了调整api编辑页面大小以适配移动端
        var width = window.innerWidth;
        if (width > 600) {
          return ["50%", "90%"];
        } else {
          return ["90%", "90%"];
        }
      }
      layui.use("table", function () {
        var table = layui.table;
        var $ = layui.jquery;
        var form = layui.form;

        //操作列事件
        table.on("tool(frienddata)", function (obj) {
          if (obj.event == "del") {
            //删除api操作
            layer.confirm(
              //弹窗确认是否进行删除操作
              "是否删除？",
              {
                icon: 3,
                title: "重要提示",
              },
              function (index) {
                //点击确定后发送AJAX请求
                $.getJSON(
                  "/home/delfriend",
                  {
                    id: obj.data.id, //发送要删除的api的ID
                  },
                  function (response) {
                    if (response.code != 200) {
                      layer.msg(response.msg);
                    } else {
                      obj.del(); //删除当前行DOM
                      layer.close(index); //关闭弹出层
                      layer.msg(response.msg);
                      table.reload("ID-friend-data", {
                        scrollPos: "fixed", // 保持滚动条位置不变,重载表格数据
                        where: {
                          nowTime: new Date().getTime(),
                        },
                      });
                    }
                  }
                );
              }
            );
          } else if (obj.event == "edit") {
            //编辑
            console.log("编辑");
            layer.open({
              type: 2,
              title: "修改API信息",
              content: "/home/editfriend",
              area: setpage(),
              end: function () {
                //表格数据刷新
                table.reload("ID-friend-data", {
                  where: {
                    nowTime: new Date().getTime(),
                  },
                });
              },
              success: function (layero, index) {
                //修改成功后将editapi页面回显数据

                var body = layer.getChildFrame("body", index); //获取edit对象
                body.find("#friend-name").val(obj.data.friend_name); //回显名称
                body.find("#friend-url").val(obj.data.friend_url); //回显地址
                body.find("#friend-icon-url").val(obj.data.friend_icon_url); //回显头像地址
                body.find("#friend-doc").val(obj.data.friend_doc); //回显简介
                body.find("input[name='id']").attr({
                  //设置api ID方便后续对接接口
                  value: obj.data.id,
                });
              },
            });
          }
        });

        // 已知数据渲染
        var inst = table.render({
          elem: "#ID-friend-data",
          page: true, //开启分页
          url: "/home/frienddata/?nowTime=" + new Date().getTime(), //api信息请求接口
          limit: 10, //每页显示条数
          cols: [
            [
              {
                field: "id",
                title: "ID",
                width: 10,
                sort: true,
                fixed: "left",
              },
              {
                field: "friend_name",
                width: 200,
                title: "友链名称",
              },
              {
                field: "friend_url",
                width: 300,
                title: "友链地址",
              },
              {
                field: "friend_icon_url",
                width: 300,
                title: "友链头像地址",
              },
              {
                field: "friend_doc",
                width: 200,
                title: "友链简介",
              },
              {
                title: "操作",
                width: 150,
                align: "center",
                templet: function () {
                  var str =
                    '<button type="button" class="layui-btn layui-btn-xs layui-btn-danger" lay-event=\'del\'>删除</button>';
                  str +=
                    '<button type="button" class="layui-btn layui-btn-xs layui-btn-normal" lay-event=\'edit\'>修改</button>';
                  return str;
                },
              },
            ],
          ],
        });

        //给添加API按钮绑定事件
        $("#add-friend").click(function () {
          //点击添加按钮时弹出一个表单弹出层
          layer.open({
            type: 2,
            title: "添加友链信息",
            content: "home/addfriend",
            area: setpage(),
            end: function () {
              //表格数据刷新
              table.reload("ID-friend-data", {
                where: {
                  nowTime: new Date().getTime(),
                },
              });
            },
          });
        });
      });
    </script>
  </body>
</html>
