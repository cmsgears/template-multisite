call ../../yii migrate/up --migrationPath=@cmsgears/module-core/migrations --interactive=0
call ../../yii migrate/up --migrationPath=@cmsgears/module-forms/migrations --interactive=0
call ../../yii migrate/up --migrationPath=@cmsgears/module-newsletter/migrations --interactive=0
call ../../yii migrate/up --migrationPath=@cmsgears/module-notify/migrations --interactive=0
call ../../yii migrate/up --migrationPath=@cmsgears/plugin-file-manager/migrations --interactive=0
call ../../yii migrate/up --migrationPath=@cmsgears/plugin-social-meta/migrations --interactive=0
call ../../yii migrate/up --migrationPath=@foxslider/cmg-plugin/migrations --interactive=0
call ../../yii migrate/up --migrationPath=@cmsgears/module-sns-connect/migrations --interactive=0
call ../../yii core-console/theme/up --migrationPath=@themes/blog/migrations --interactive=0 --default=0 --activate=0
call ../../yii core-console/theme/up --migrationPath=@themes/t24x7/migrations --interactive=0
call ../../yii migrate/up --migrationPath=@console/migrations/data --interactive=0
