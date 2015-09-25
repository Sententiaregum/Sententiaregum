/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

import React from 'react';
import MenuConfig from '../../menu/model/MenuItem.js';

/**
 * @author Benjamin Bieler <benjaminbieler2014@gmail.com>
 */
class Menu extends React.Component {
    /**
     * Builds a virtual dom for the menu section.
     *
     * @returns {XML}
     */
    render() {
        let api = this;
        let items = this.props.items;
        let menuItems = [];

        if (MenuConfig.permissionRule == 'User') {
            for (var i of items) {
                menuItems.push(
                    <li className={api.getMenuClass(items[i].url)} key={i}>
                        <a href={items[i].url}>
                            {SenTranslation.trans(items[i].label, {})}
                        </a>
                    </li>
                );
            }
        }

        return (
            <nav className="top-bar" data-topbar role="navigation">
                <ul className="title-area">
                    <li className="name">
                        <h1><a href="/">Sententiaregum</a></h1>
                    </li>
                    <li className="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
                </ul>
                <section className="top-bar-section">
                    <ul className="right">
                        {menuItems}
                    </ul>
                </section>
            </nav>
        );
    }
}

export default Menu;
