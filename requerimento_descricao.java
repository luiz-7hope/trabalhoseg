descricao.java:
public class descricao {

    private int id;
    private String tipo;
    private String descricao;
    private String status;
    private String data;

    public descricao(int id, String tipo, String descricao, String status, String data) {
        this.id = id;
        this.tipo = tipo;
        this.descricao = descricao;
        this.status = status;
        this.data = data;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getTipo() {
        return tipo;
    }

    public void setTipo(String tipo) {
        this.tipo = tipo;
    }

    public String getDescricao() {
        return descricao;
    }

    public void setDescricao(String descricao) {
        this.descricao = descricao;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getData() {
        return data;
    }

    public void setData(String data) {
        this.data = data;
    }

    @Override
    public String toString() {
        return "ID: " + id +
               " | Tipo: " + tipo +
               " | Descrição: " + descricao +
               " | Status: " + status +
               " | Data: " + data;
    }
}
