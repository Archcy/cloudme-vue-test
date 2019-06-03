/*****A Server - Key Server ******/
create table
A_User_Control
(
    UserID int unsigned AUTO_INCREMENT,
    User char(16),
    Pass char(60) not null,
    Statu tinyint default 0,
    primary key(User),
    unique(UserID)
);

create table
A_User_Info
(
    UserID int unsigned,
    Sex  char(1),
    Regdate DATETIME DEFAULT CURRENT_TIMESTAMP,
    VIPend DATETIME,
    mail varchar(31),
    Phone varchar(16),
    primary key(UserID)
);


create table
A_User_Key
(
    UserID int unsigned,
    FID int unsigned,
    Fkey char(44),
    Fiv char(24),
    primary key(UserID,FID)
);

create table
A_User_Hash
(
    UserID int unsigned,
    FID int unsigned,
    FileHash char(64),
    primary key(UserID,FID)
);

/*****B Server - File Server ****/
create table
B_User_Control
(
    UserID int unsigned AUTO_INCREMENT,
    User char(16),
    Pass char(60) not null,
    Statu tinyint default 0,
    primary key(User),
    unique(UserID)
);

create table
B_User_Info
(
    UserID int unsigned,
    VIPend DATETIME,
    mail varchar(31),
    Phone varchar(16),
    StorageTotal bigint default 34359738368,
    StorageUsed bigint default 0,
    primary key(UserID)
);


create table
B_File_Index
(
    UserID int unsigned,
    FID int unsigned AUTO_INCREMENT,
    primary key(UserID,FID)
)ENGINE=MyISAM;


create table
B_File_Info
(
    UserID int unsigned,
    FID int unsigned,
    FName varchar(255) not null,
    Fsize int not null,
    FPID int unsigned not null,
    FShare tinyint default 0,
    Ftype tinyint default 0,
    Fav tinyint default 0,
    Ftime DATETIME DEFAULT CURRENT_TIMESTAMP,
    primary key(UserID,FID)
);

create table
B_File_Share
(
    UserID int unsigned,
    FID int unsigned,
    FDIR varchar(255) not null,
    primary key(UserID,FID)
);


