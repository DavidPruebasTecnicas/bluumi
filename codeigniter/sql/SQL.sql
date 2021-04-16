CREATE DATABASE pixomaticdfv;

CREATE TABLE pixomaticdfv.companies (
    id bigint NOT NULL AUTO_INCREMENT,
    name varchar(50) UNIQUE NOT NULL,
    cif char(9) UNIQUE NOT NULL,
    shortdesc varchar(100) DEFAULT NULL,
    description TEXT NOT NULL,
    email varchar(50) NOT NULL,
    ccc char(4) UNIQUE DEFAULT NULL,
    date DATE DEFAULT NULL,
    status boolean DEFAULT NULL,
    logo varchar(999) NOT NULL,
    token varchar(255) NOT NULL,
    deleted boolean default false,
    created_at timestamp default current_timestamp, 
    updated_at timestamp,
    deleted_at timestamp,
    PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


CREATE TABLE pixomaticdfv.favorites (
    id bigint NOT NULL AUTO_INCREMENT,
    idOwner bigint not null,
    idCompanie bigint not null,
    PRIMARY KEY (id),
    FOREIGN KEY (idCompanie) REFERENCES companies(id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;