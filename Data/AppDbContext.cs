using System;
using System.Collections.Generic;
using System.IO;
using System.Text.Json;
using TodoList.Models;

namespace TodoList.Data
{
    public static class BancoDados
    {
        private static readonly string caminhoArquivo = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "Data", "dados.json");


        public static List<TodoTask> Tarefas { get; private set; } = new List<TodoTask>();

        public static void CarregarDados()
        {
            if (File.Exists(caminhoArquivo))
            {
                string json = File.ReadAllText(caminhoArquivo);
                Tarefas = JsonSerializer.Deserialize<List<TodoTask>>(json) ?? new List<TodoTask>();
            }
            else
            {
                Tarefas = new List<TodoTask>();
            }
        }

        public static void SalvarDados()
        {
            string pasta = Path.GetDirectoryName(caminhoArquivo);
            if (!Directory.Exists(pasta))
            {
                Directory.CreateDirectory(pasta);
            }

            string json = JsonSerializer.Serialize(Tarefas, new JsonSerializerOptions { WriteIndented = true });
            File.WriteAllText(caminhoArquivo, json);
        }
    }
}
