/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React                  from 'react';
import MenuWrapper            from './menu/MenuWrapper';
import Menu                   from './menu/Menu';
import { connect            } from 'react-redux';
import { bindActionCreators } from 'redux';
import *  as menuActions      from '../../../actions/menuActions';

const AppMenu = ({ items, actions }) => {
  return (
    <MenuWrapper>
      <Menu items={items} actions={actions}/>
    </MenuWrapper>
  );
};

AppMenu.propTypes = {
  config: React.PropTypes.arrayOf(React.PropTypes.object)
};

const mapStateToProps = state => ({
  items: state.menu.items
});

const mapDispatchToProps = dispatch => ({
  actions: bindActionCreators(menuActions, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(AppMenu);
