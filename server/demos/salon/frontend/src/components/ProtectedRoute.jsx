import { useContext } from 'react';
import { Navigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';

function ProtectedRoute({ children }) {
    const { user, loading } = useContext(AuthContext);

    if (loading) {
        return (
            <div className="h-screen flex items-center justify-center" style={{ background: '#f8fafc' }}>
                <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#a42ca1]"></div>
            </div>
        );
    }

    if (!user) {
        return <Navigate to="/login" replace />;
    }

    return children;
}

export default ProtectedRoute;
