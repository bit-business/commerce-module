export const label = "Hrvatski";

export default {
  vuelidate: {
    error: "Ovo polje je nevažeće",
  },
  action: {
    bin: "Obriši",
    bulkRestore: "Masovno vraćanje",
    bulkDeletePermanent: "Masovno trajno brisanje",
    restore: {
      title: "Vrati stavku",
      text: "Jeste li sigurni da želite vratiti ovu stavku?",
      accept: "Vrati",
      cancel: "Odustani",
    },
  },
  product: {
    browse: {
      title: "Proizvod",
      header: {
        name: "Naziv proizvoda",
        slug: "Slug",
        productCategoryId: "Kategorija",
        productImage: "Slika proizvoda",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    browseBin: {
      title: "Kanta za proizvode",
      header: {
        name: "Naziv proizvoda",
        slug: "Slug",
        productCategoryId: "Kategorija",
        productImage: "Slika proizvoda",
        deletedAt: "Obrisano",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    add: {
      title: "Dodaj proizvod",
      field: {
        name: {
          title: "Naziv proizvoda",
          placeholder: "Naziv proizvoda",
        },
        slug: {
          title: "Slug",
          placeholder: "Slug",
        },
        productCategoryId: {
          title: "Kategorija proizvoda",
          placeholder: "Kategorija proizvoda",
        },
        productImage: {
          title: "Slika proizvoda",
          placeholder: "Slika proizvoda",
        },
        desc: {
          title: "Opis",
          placeholder: "Opis",
        },
      },
      header: {
        productImage: "Slika proizvoda",
        name: "Naziv proizvoda",
        quantity: "Količina",
        price: "Cijena",
        discount: "Popust",
        SKU: "SKU",
        action: "Akcija",
      },
      button: "Spremi",
      detail: {
        title: "Detalji proizvoda",
        add: {
          title: "Dodaj detalje proizvoda",
          field: {
            name: {
              title: "Naziv detalja proizvoda",
              placeholder: "Naziv detalja proizvoda",
            },
            quantity: {
              title: "Zaliha proizvoda",
              placeholder: "Zaliha proizvoda",
            },
            price: {
              title: "Cijena proizvoda",
              placeholder: "Cijena proizvoda",
            },
            SKU: {
              title: "SKU detalja proizvoda",
              placeholder: "SKU detalja proizvoda",
            },
            productImage: {
              title: "Slika detalja proizvoda",
              placeholder: "Slika detalja proizvoda",
            },
            discount: {
              title: "Popust",
              placeholder: "Popust",
            },
          },
          button: {
            save: "Spremi",
            cancel: "Odustani",
          },
        },
        edit: {
          title: "Uredi detalje proizvoda",
          field: {
            name: {
              title: "Naziv detalja proizvoda",
              placeholder: "Naziv detalja proizvoda",
            },
            quantity: {
              title: "Zaliha proizvoda",
              placeholder: "Zaliha proizvoda",
            },
            price: {
              title: "Cijena proizvoda",
              placeholder: "Cijena proizvoda",
            },
            SKU: {
              title: "SKU detalja proizvoda",
              placeholder: "SKU detalja proizvoda",
            },
            productImage: {
              title: "Slika detalja proizvoda",
              placeholder: "Slika detalja proizvoda",
            },
            discount: {
              title: "Popust",
              placeholder: "Popust",
            },
          },
          button: {
            save: "Spremi",
            cancel: "Odustani",
          },
        },
      },
    },
    detail: {
      title: "Detalji proizvoda",
      header: {
        name: "Naziv proizvoda",
        slug: "Slug",
        desc: "Opis",
        productCategory: "Kategorija proizvoda",
        productImage: "Slika proizvoda",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        deletedAt: "Obrisano",
      },
    },
  },
  productCategories: {
    browse: {
      title: "Kategorije proizvoda",
      header: {
        name: "Naziv kategorije",
        slug: "Slug",
        children: "Podkategorije",
        desc: "Opis",
        SKU: "SKU",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    browseBin: {
      title: "Obrisani proizvodi za kategorije proizvoda",
      header: {
        name: "Naziv kategorije",
        slug: "Slug",
        children: "Podkategorije",
        desc: "Opis",
        SKU: "SKU",
        deletedAt: "Obrisano",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    add: {
      title: "Dodaj kategoriju proizvoda",
      field: {
        name: {
          title: "Naziv kategorije",
          placeholder: "Naziv kategorije",
        },
        slug: {
          title: "Slug",
          placeholder: "Slug",
        },
        desc: {
          title: "Opis",
          placeholder: "Opis",
        },
        parent: {
          title: "Roditeljska kategorija",
          placeholder: "Roditeljska kategorija",
        },
        SKU: {
          title: "SKU kategorije",
          placeholder: "SKU kategorije",
        },
        image: {
          title: "Slika",
          placeholder: "Slika",
        },
      },
      button: "Spremi",
    },
    edit: {
      title: "Uredi kategoriju proizvoda",
      field: {
        name: {
          title: "Naziv kategorije",
          placeholder: "Naziv kategorije",
        },
        slug: {
          title: "Slug",
          placeholder: "Slug",
        },
        desc: {
          title: "Opis",
          placeholder: "Opis",
        },
        parent: {
          title: "Roditeljska kategorija",
          placeholder: "Roditeljska kategorija",
        },
        SKU: {
          title: "SKU kategorije",
          placeholder: "SKU kategorije",
        },
        image: {
          title: "Slika",
          placeholder: "Slika",
        },
      },
      button: "Spremi",
    },
    detail: {
      title: "Detalji kategorije proizvoda",
      header: {
        name: "Naziv kategorije",
        slug: "Slug",
        desc: "Opis",
        SKU: "SKU",
        image: "Slika",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        deletedAt: "Obrisano",
        action: "Akcija",
      },
    },
  },
  discounts: {
    discountType: {
      fixed: "Fiksno",
      percent: "Postotak",
    },
    help: {
      discountFixed: "Fiksni popust je nominalni iznos postavljenog popusta. Unesite nominalni iznos popusta bez postavljenog razdjelnika",
      discountPercent: "Postotni popust je nominalni iznos postavljenog popusta u vrijednosti između 1-100",
      percentType: "Fiksni popust je nominalni iznos postavljenog popusta, a nominalni iznos popusta unosite bez razdjelnika. Postotni popust je nominalni iznos postavljenog popusta u vrijednosti između 1-100",
    },
    browse: {
      title: "Popusti na proizvode",
      header: {
        name: "Naziv popusta",
        desc: "Opis",
        discountType: "Vrsta popusta",
        discountPercent: "Postotak popusta",
        discountFixed: "Fiksni popust",
        active: "Aktivan",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    browseBin: {
      title: "Obrisani proizvodi za popuste",
      header: {
        name: "Naziv popusta",
        desc: "Opis",
        discountType: "Vrsta popusta",
        discountPercent: "Postotak popusta",
        discountFixed: "Fiksni popust",
        active: "Aktivan",
        deletedAt: "Obrisano",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    add: {
      title: "Dodaj popust",
      field: {
        name: {
          title: "Naziv popusta",
          placeholder: "Naziv popusta",
        },
        desc: {
          title: "Opis",
          placeholder: "Opis",
        },
        discountType: {
          title: "Vrsta popusta",
          placeholder: "Vrsta popusta",
        },
        discountPercent: {
          title: "Postotak",
          placeholder: "Postotak",
        },
        discountFixed: {
          title: "Fiksno",
          placeholder: "Fiksno",
        },
        active: {
          title: "Aktivan",
          placeholder: "Aktivan",
        },
      },
      button: "Spremi",
    },
    edit: {
      title: "Uredi popust",
      field: {
        name: {
          title: "Naziv popusta",
          placeholder: "Naziv popusta",
        },
        desc: {
          title: "Opis",
          placeholder: "Opis",
        },
        discountType: {
          title: "Vrsta popusta",
          placeholder: "Vrsta popusta",
        },
        discountPercent: {
          title: "Postotak",
          placeholder: "Postotak",
        },
        discountFixed: {
          title: "Fiksno",
          placeholder: "Fiksno",
        },
        active: {
          title: "Aktivan",
          placeholder: "Aktivan",
        },
      },
      button: "Spremi",
    },
    detail: {
      title: "Detalji popusta",
      header: {
        name: "Naziv popusta",
        desc: "Opis",
        discountType: "Vrsta popusta",
        discountPercent: "Postotak popusta",
        discountFixed: "Fiksni popust",
        active: "Aktivan",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        deletedAt: "Obrisano",
        action: "Akcija",
      },
    },
  },
  orders: {
    status: {
      "-1": "Neuspjelo",
      0: "Čeka uplatu",
      1: "Čeka potvrdu",
      2: "Obrada",
      3: "Dostava",
      4: "Primljeno",
    },
    browse: {
      title: "Narudžbe",
      header: {
        orderId: "ID narudžbe",
        user: "Korisnik",
        discounted: "S popustom",
        total: "Ukupno",
        payed: "Plaćeno",
        status: "Status",
        orderedAt: "Naručeno",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    confirm: {
      title: {
        customerInfo: "Informacije o kupcu",
        orderInfo: "Informacije o narudžbi",
        trackingNumber: "Postavi broj praćenja",
        orderPayment: "Plaćanje narudžbe",
        cancel: "Otkaži",
      },
      header: {
        recipientName: "Ime primatelja",
        user: {
          email: "Email",
        },
        addressLine1: "Adresa 1",
        addressLine2: "Adresa 2",
        city: "Grad",
        postalCode: "Poštanski broj",
        country: "Država",
        phoneNumber: "Broj telefona",
        total: "Ukupno",
        status: "Status narudžbe",
        discounted: "S popustom",
        payed: "Za platiti",
        proof: "Dokaz o transakciji",
        cancelMessage: "Poruka o otkazivanju",
        shippingCost: "Trošak dostave",
        trackingNumber: "Broj za praćenje",
        expiredAt: "Ističe",
        action: "Akcija",
        orderPayment: {
          sourceBank: "Izvorna banka",
          destinationBank: "Odredišna banka",
          accountNumber: "Broj računa",
          totalTransfer: "Ukupno preneseno",
          proofOfTransaction: "Dokaz o transakciji",
        },
      },
      field: {
        trackingNumber: {
          label: "Broj za praćenje",
          placeholder: "Broj za praćenje",
        },
        cancel: {
          label: "Poruka o otkazivanju",
          placeholder: "Poruka o otkazivanju",
        },
      },
      button: {
        save: "Spremi",
      },
    },
  },

  cart: {
    browse: {
      title: "Košarica korisnika",
      header: {
        id: "ID",
        name: "Ime korisnika & Email",
        productName: "Naziv proizvoda",
        quantity: "Količina",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    detail: {
      title: "Detalji košarice korisnika",
      header: {
        id: "ID",
        name: "Ime korisnika",
        productName: "Naziv proizvoda",
        quantity: "Količina",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        action: "Akcija",
      },
    },
  },

  userAddress: {
    browse: {
      title: "Adrese korisnika",
      header: {
        name: "Ime korisnika",
        email: "Email",
        address1: "Adresa 1",
        address2: "Adresa 2",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    detail: {
      title: "Detalji adrese korisnika",
      header: {
        name: "Ime korisnika",
        email: "Email",
        address1: "Adresa 1",
        address2: "Adresa 2",
        city: "Grad",
        postalCode: "Poštanski broj",
        country: "Država",
        phoneNumber: "Broj telefona",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        action: "Akcija",
      },
    },
  },

  commerceSite: {
    action: "Akcija",
    deletedImage: {
      title: "Obrisana slika",
      text: "Odabrana slika je uspješno obrisana",
    },
    configUpdated: "Konfiguracija ažurirana",
    add: {
      title: "Dodaj konfiguraciju trgovine",
      field: {
        displayName: {
          title: "Prikazano ime",
          placeholder: "Primjer: Prikazano ime",
        },
        key: {
          title: "Ključ",
          placeholder: "Primjer: ključ_primjer",
        },
        type: {
          title: "Vrsta",
          placeholder: "Vrsta",
        },
        group: {
          title: "Grupa",
          placeholder: "Grupa",
        },
        options: {
          title: "Opcije",
          description: "Opcije su potrebne za Checkbox, Radio, Select, Višestruki odabir. Primjer:",
          example: `{"items": [{"label":"Ovo je oznaka","value":"ovo_je_vrijednost"}] }`,
        },
      },
      button: "Spremi",
    },
    edit: {
      multiple: "Ažuriraj konfiguracije",
    },
  },

  productReview: {
    browse: {
      title: "Recenzije proizvoda",
      header: {
        name: "Naziv proizvoda",
        orderDate: "Datum narudžbe",
        email: "Email",
        rating: "Ocjena",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    detail: {
      title: "Detalji recenzije proizvoda",
      header: {
        name: "Naziv proizvoda",
        orderDate: "Datum narudžbe",
        email: "Email",
        rating: "Ocjena",
        review: "Recenzija",
        media: "Medij",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        action: "Akcija",
      },
    },
  },

  payments: {
    browse: {
      title: "Plaćanja",
      header: {
        name: "Ime",
        slug: "Slug",
        isActive: "Aktivno",
        action: "Akcija",
      },
      footer: {
        descriptionTitle: "Registri",
        descriptionConnector: "od",
        descriptionBody: "Stranice",
      },
    },
    add: {
      title: "Dodaj plaćanje",
      field: {
        name: {
          title: "Ime",
          placeholder: "Ime",
        },
        slug: {
          title: "Slug",
          placeholder: "Slug",
        },
        isActive: {
          title: "Aktivno",
          placeholder: "Aktivno",
        },
      },
      button: "Spremi",
    },
    edit: {
      title: "Uredi plaćanje",
      field: {
        name: {
          title: "Ime",
          placeholder: "Ime",
        },
        slug: {
          title: "Slug",
          placeholder: "Slug",
        },
        isActive: {
          title: "Aktivno",
          placeholder: "Aktivno",
        },
      },
      button: "Spremi",
    },
    detail: {
      title: "Detalji popusta",
      header: {
        name: "Naziv popusta",
        desc: "Opis",
        discountType: "Vrsta popusta",
        discountPercent: "Postotak popusta",
        discountFixed: "Fiksni popust",
        active: "Aktivno",
        createdAt: "Stvoreno",
        updatedAt: "Ažurirano",
        deletedAt: "Obrisano",
        action: "Akcija",
      },
    },
    option: {
      title: "Opcija plaćanja",
      popup: {
        add: {
          title: "Dodaj opciju plaćanja",
          field: {
            name: "Ime",
            slug: "Slug",
            description: "Opis",
            image: "Slika",
            isActive: "Aktivno",
            order: "Redoslijed",
          },
          button: {
            cancel: "Otkazati",
            add: "Dodaj",
          },
        },
        edit: {
          title: "Uredi opciju plaćanja",
          field: {
            name: "Ime",
            slug: "Slug",
            description: "Opis",
            image: "Slika",
            isActive: "Aktivno",
            order: "Redoslijed",
          },
          button: {
            cancel: "Otkazati",
            edit: "Uredi",
          },
        },
      },
    },
  },
};