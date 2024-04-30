<?php

enum EstadoLotes: string {
    case VENDIDO     = "VENDIDO";
    case LIBRE       = "LIBRE";
    case RESERVADO   = "RESERVADO";
    case DEPOSITO    = "DEPOSITO";
    case TRANSFERIDO = "TRANSFERIDO";
    case BLOQUEADO = "BLOQUEADO";
}